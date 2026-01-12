<?php

namespace App\Http\Controllers;

use App\Models\Prestamos;
use App\Models\PrestamoDetalle;
use App\Models\Socios;
use App\Models\Movimiento;
use App\Models\PagosPrestamos;
use App\Models\PagosPrestamosDetalles;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PagarPrestamoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:pagar-prestamo', ['only'=>['index','create', 'store','show']]);
    }

    public function index()
    {

        /*
        $prestamos = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
        ->leftJoin('pagos_prestamos', function ($join) {
            $join->on('prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                ->where('pagos_prestamos.pagado', '!=', 1); // Sólo pagos NO pagados
        })
        ->where('prestamos.estatus', 'AUTORIZADO')
        ->where('prestamos.prestamo_especial', 0)
        ->groupBy('prestamos.socios_id', 'socios.nombre_completo', 'socios.id')
        ->having('total_debe', '>', 0)
        ->selectRaw('
            socios.id,
            socios.nombre_completo,
            SUM(prestamos.debe) as total_debe,
            COALESCE(SUM(pagos_prestamos.capital), 0) as total_capital_pendiente
        ')
        ->get();
        */

        $prestamos = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
            ->leftJoin('pagos_prestamos', function ($join) {
                $join->on('prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                    ->where('pagos_prestamos.pagado', '!=', 1);
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pagos_prestamos as pp1')
                    ->whereColumn('pp1.prestamos_id', 'prestamos.id')
                    ->where('pp1.pagado', '!=', 1)
                    ->whereRaw('pp1.serie_pago = (
                        SELECT MAX(pp2.serie_pago)
                        FROM pagos_prestamos as pp2
                        WHERE pp2.prestamos_id = pp1.prestamos_id
                    )');
            })
            ->where('prestamos.estatus', 'AUTORIZADO')
            ->where('prestamos.prestamo_especial', 0)
            ->groupBy('prestamos.socios_id', 'socios.nombre_completo', 'socios.id')
            ->having('total_debe', '>', 0)
            ->selectRaw('
                socios.id,
                socios.nombre_completo,
                SUM(prestamos.debe) as total_debe,
                COALESCE(SUM(pagos_prestamos.capital), 0) as total_capital_pendiente
            ')
        ->get();


        return view('pagar_prestamos.index', compact('prestamos'));
    }

    public function create()
    {
        //
    }

    public function storeNormal(Request $request)
    {
        try {

            $pagosIds = $request->prestamos_id ?? [];

            //  Validar que haya al menos un pago seleccionado
            if (empty($pagosIds)) {
                return redirect()->back()
                ->with('error', 'Debe seleccionar al menos un pago.')
                ->withInput();
            }

            $esAdelantado = count($pagosIds) > 1;

            $fechaUltimoDescuento = Carbon::createFromFormat(
                'Y-m-d',
                $request->fecha_ultimo_descuento
            )->startOfDay();

            $pagosSeleccionados = PagosPrestamos::whereIn('id', $pagosIds)
            ->orderBy('prestamos_id')
            ->orderBy('serie_pago')
            ->get()
            ->groupBy('prestamos_id');

            foreach ($pagosSeleccionados as $prestamoId => $pagosPrestamo) {

                // Series seleccionadas
                $seriesSeleccionadas = $pagosPrestamo->pluck('serie_pago')->values();

                // Series válidas reales (según fecha)
                $seriesValidas = PagosPrestamos::where('prestamos_id', $prestamoId)
                    ->where('pagado', 0)
                    ->whereDate('fecha_tabla', '>', $fechaUltimoDescuento)
                    ->orderBy('serie_pago')
                    ->pluck('serie_pago')
                    ->values();

                if ($seriesValidas->isEmpty()) {
                    return redirect()->back()
                        ->with(
                            'error',
                            "El préstamo {$prestamoId} no tiene pagos posteriores al último descuento."
                        )
                        ->withInput();
                }

                // 🔴 1) Debe iniciar en la primera serie válida
                if ($seriesSeleccionadas->first() !== $seriesValidas->first()) {
                    return redirect()->back()
                        ->with(
                            'error',
                            "El préstamo {$prestamoId} debe iniciar desde la serie {$seriesValidas->first()}."
                        )
                        ->withInput();
                }

                // 🔴 2) No permitir saltos
                for ($i = 0; $i < $seriesSeleccionadas->count(); $i++) {

                    if (!isset($seriesValidas[$i]) || $seriesSeleccionadas[$i] !== $seriesValidas[$i]) {
                        return redirect()->back()
                            ->with(
                                'error',
                                "Las series del préstamo {$prestamoId} deben ser consecutivas sin saltos."
                            )
                            ->withInput();
                    }
                }
            }

            // ✅ Si pasa la validación, ahora sí inicia la transacción
            \DB::beginTransaction();

            $socioID = $request->socios_id;
            $datosFormulario = $request->all();
            $prestamosIds = [];
            $formaPago = $request->forma_pago;
            //$afectaSaldoSocio = $formaPago !== 'LIQUIDAR PRÉSTAMO - REESTRUCTURACIÓN';
            $afectaSaldoSocio = !in_array($formaPago, [
                'LIQUIDAR PRÉSTAMO - REESTRUCTURACIÓN',
                'LIQUIDAR PRÉSTAMO - PAGO TOTAL'
            ]);
            //$totalInteresesAdelantados = 0;

            $interesesPorPrestamo = [];

            if (!empty($pagosIds)) {
                foreach ($pagosIds as $pagoId) {

                    // Obtener el registro de pago exacto
                    $pago = PagosPrestamos::where('id', $pagoId)
                        ->where('pagado', 0)
                        ->firstOrFail();

                    // ACUMULAR INTERES POR PRÉSTAMO INDIVIDUAL
                    $prestamoId = $pago->prestamos_id;

                    if (!isset($interesesPorPrestamo[$prestamoId])) {
                        $interesesPorPrestamo[$prestamoId] = 0;
                    }

                    $interesesPorPrestamo[$prestamoId] += $pago->interes ?? 0;

                    // 🔹 Acumular intereses de esta serie
                    //$totalInteresesAdelantados += $pago->interes ?? 0;

                    // Guardas los IDs para enviarlos después
                    if ($pago->prestamos_id > 0) {
                        $prestamosIds[] = $pago->prestamos_id;
                    }

                    // Monto del pago
                    $pagoCapital = $pago->capital;
                    $idprestamoPago = $pago->id;

                    // Actualizar pago a pagado
                    $pago->update([
                        'pagado'       => 1,
                        'forma_pago'   => $request->forma_pago,
                        'metodo_pago'  => $request->metodo_pago,
                        'referencia'   => $request->referencia,
                        'fecha_pago'   => now(),
                        'fecha_captura'=> now(),
                        'es_adelantado' => $esAdelantado,
                        'es_reversion'  => 0,
                        'reversion_de'  => null,
                        'wci'          => auth()->user()->id,
                    ]);

                    // Obtener préstamo
                    $prestamo = Prestamos::findOrFail($pago->prestamos_id);

                    // ----------- Lógica de avales -----------
                    $avales = PrestamoDetalle::where('prestamos_id', $prestamo->id)
                        ->where('debe', '>', 0)
                        ->get();

                    if ($avales->count() > 0) {
                        // Distribuir entre avales
                        $totalAvales = $avales->count();
                        $abonoAval   = $pagoCapital / $totalAvales;
                        $sumaAbonosAval = 0;

                        foreach ($avales as $row) {
                            $rowPrestamo = Prestamos::findOrFail($row->prestamos_id);

                            $restanteAval = $row->debe;
                            $abonoReal = min($abonoAval, $restanteAval);

                            $sumaAbonosAval += $abonoReal;

                            // Guardar detalle de pago
                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id'       => $row->prestamos_id,
                                'socios_id'          => $row->socios_id,
                                'tipo_cliente'       => 'AVAL',
                                'abona'              => $abonoReal,
                                'es_adelantado'      => $esAdelantado,
                                'es_reversion'       => 0,
                                'reversion_de'       => null,
                                'wci'                => auth()->user()->id,
                            ]);

                            // Actualizar préstamo del aval
                            $rowPrestamo->increment('abona', $abonoReal);
                            $rowPrestamo->decrement('debe', $abonoReal);
                            $rowPrestamo->update([
                                'fecha_pago_reestructuracion' => $request->fecha_ultimo_descuento,
                                'monto_pago_reestructuracion' => $pagoCapital,
                            ]);

                            // Actualizar socio aval
                            $aval = Socios::find($row->socios_id);

                            $movimiento = $prestamo->movimientos()->create([
                                'socios_id'       => $row->socios_id,
                                'fecha'           => now(),
                                'folio'           => 'MOV-',
                                'saldo_anterior'  => $aval->saldo,
                                'saldo_actual'    => $aval->saldo,
                                'monto'           => $abonoReal,
                                'movimiento'      => 'PAGO PRÉSTAMO -AVAL-',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago'     => 'EFECTIVO',
                                'estatus'         => 'EFECTUADO',
                            ]);

                            $movimiento->update([
                                'folio' => 'MOV-' . $movimiento->id,
                            ]);

                            $aval->decrement('monto_prestamos', $abonoReal);

                            // Actualizar detalle aval
                            $row->increment('abona', $abonoReal);
                            $row->decrement('debe', $abonoReal);
                            $row->update([
                                'fecha_pago_reestructuracion' =>$request->fecha_ultimo_descuento,
                                'monto_pago_reestructuracion' => $abonoReal
                            ]);
                        }

                        // Lo que sobra para el socio principal
                        $capitalRestante = $pagoCapital - $sumaAbonosAval;
                        if ($capitalRestante > 0) {
                            $interes = $prestamo->total_intereses / $prestamo->total_quincenas;

                            $prestamo->increment('abona', $capitalRestante);
                            $prestamo->decrement('debe', $capitalRestante);
                            $prestamo->update([
                                'fecha_pago_reestructuracion' => $request->fecha_ultimo_descuento,
                                'monto_pago_reestructuracion' => $pagoCapital,
                            ]);

                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id'       => $prestamo->id,
                                'socios_id'          => $prestamo->socios_id,
                                'tipo_cliente'       => 'SOCIO',
                                'abona'              => $capitalRestante + $interes,
                                'es_adelantado'      => $esAdelantado,
                                'es_reversion'       => 0,
                                'reversion_de'       => null,
                                'wci'                => auth()->user()->id,
                            ]);

                            // Movimiento del socio
                            $socio = Socios::find($prestamo->socios_id);

                            $movimiento = $prestamo->movimientos()->create([
                                'socios_id'       => $socio->id,
                                'fecha'           => now(),
                                'folio'           => 'MOV-',
                                'saldo_anterior'  => $socio->saldo,
                                'saldo_actual'    => $socio->saldo - ($capitalRestante + $interes),
                                'monto'           => $capitalRestante + $interes,
                                'movimiento'      => 'PAGO PRÉSTAMO -AVAL-',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago'     => 'EFECTIVO',
                                'estatus'         => 'EFECTUADO',
                            ]);

                            $movimiento->update([
                                'folio' => 'MOV-' . $movimiento->id,
                            ]);

                            $socio->decrement('monto_prestamos', $capitalRestante);
                            //$socio->decrement('monto_prestamos', $capitalRestante + $interes);
                            //$socio->decrement('saldo', $capitalRestante + $interes);
                        }
                    } else {
                        // ----------- Sin avales -----------
                        $abonoReal = min($pagoCapital, $prestamo->debe);

                        $prestamo->increment('abona', $abonoReal);
                        $prestamo->decrement('debe', $abonoReal);
                        $prestamo->update([
                            'fecha_pago_reestructuracion' => $request->fecha_ultimo_descuento,
                            'monto_pago_reestructuracion' => $pagoCapital,
                        ]);

                        PagosPrestamosDetalles::create([
                            'pagos_prestamos_id' => $idprestamoPago,
                            'prestamos_id'       => $prestamo->id,
                            'socios_id'          => $prestamo->socios_id,
                            'tipo_cliente'       => 'SOCIO',
                            'abona'              => $abonoReal,
                            'es_adelantado'      => $esAdelantado,
                            'es_reversion'       => 0,
                            'reversion_de'       => null,
                            'wci'                => auth()->user()->id,
                        ]);

                        $socio = Socios::find($prestamo->socios_id);
                        $lastId = Movimiento::max('id') ?? 0;
                        $saldoActual = $socio->saldo;
                        if ($afectaSaldoSocio) {
                            $saldoActual = $socio->saldo - $abonoReal;
                        }

                        $movimiento = $prestamo->movimientos()->create([
                            'socios_id'       => $socio->id,
                            'fecha'           => now(),
                            'folio'           => 'MOV-',
                            'saldo_anterior'  => $socio->saldo,
                            'saldo_actual'    => $saldoActual,
                            'monto'           => $abonoReal,
                            'movimiento'      => 'PAGO PRÉSTAMO',
                            'tipo_movimiento' => 'ABONO',
                            'metodo_pago'     => 'EFECTIVO',
                            'estatus'         => 'EFECTUADO',
                        ]);

                        $movimiento->update([
                            'folio' => 'MOV-' . $movimiento->id,
                        ]);

                        $socio->decrement('monto_prestamos', $abonoReal);

                        //VALIDA SI ES POR PAGO CON SALDO DEL SOCIO 'LIQUIDACIÓN DE PRÉSTAMO - REESTRUCTURACIÓN',
                        if ($afectaSaldoSocio) {
                            $socio->decrement('saldo', $abonoReal);
                        }
                    }
                }

                // CONDONACIÓN DE INTERESES
                foreach ($interesesPorPrestamo as $prestamoId => $interesCondonado) {

                    if ($interesCondonado <= 0) {
                        continue;
                    }

                    $prestamo = Prestamos::find($prestamoId);
                    if (!$prestamo) {
                        continue;
                    }

                    $socio = Socios::find($prestamo->socios_id);
                    $lastId = Movimiento::max('id') ?? 0;

                    // 🔹 Ajuste contable del préstamo
                    $prestamo->increment('abona', $interesCondonado);
                    $prestamo->decrement('debe', $interesCondonado);

                    // 🔹 Movimiento contable (NO afecta saldo)
                    $movimiento = $prestamo->movimientos()->create([
                        'socios_id'       => $prestamo->socios_id,
                        'fecha'           => now(),
                        'folio'           => 'MOV-',
                        'saldo_anterior'  => $socio->saldo,
                        'saldo_actual'    => $socio->saldo,
                        'monto'           => $interesCondonado,
                        'movimiento'      => 'CONDONACIÓN DE INTERESES POR ADELANTO',
                        'tipo_movimiento' => 'AJUSTE',
                        'metodo_pago'     => 'SISTEMA',
                        'estatus'         => 'EFECTUADO',
                    ]);

                    $movimiento->update([
                        'folio' => 'MOV-' . $movimiento->id,
                    ]);
                }

                // Eliminar IDs duplicados
                $prestamosIds = array_unique($prestamosIds);

                foreach ($prestamosIds as $id) {

                    $liquidado = $this->verificarFinPrestamo($id);

                    // 🟡 Solo si NO se liquidó, actualizar serie real
                    if (!$liquidado) {

                        $ultimaSeriePagada = PagosPrestamos::where('prestamos_id', $id)
                            ->where('pagado', 1)
                            ->max('serie_pago');

                        if (!is_null($ultimaSeriePagada)) {
                            Prestamos::where('id', $id)->update([
                                'serie' => $ultimaSeriePagada
                            ]);
                        }
                    }
                }

                // Actualizar próxima fecha de pago
                $fechaComparar = now()->toDateString();
                $prestamoFecha = Prestamos::join('pagos_prestamos', 'pagos_prestamos.prestamos_id', '=', 'prestamos.id')
                    ->whereDate('pagos_prestamos.fecha_captura', $fechaComparar)
                    ->select('pagos_prestamos.fecha_captura', 'prestamos.proximo_pago', 'prestamos.id')
                    ->get();

                foreach ($prestamoFecha as $row) {
                    $fechaActual = Carbon::parse($row->proximo_pago);
                    if ($fechaActual->isLastOfMonth()) {
                        $nuevaFecha = $fechaActual->addDays(15);
                    } elseif ($fechaActual->day == 15) {
                        $nuevaFecha = $fechaActual->endOfMonth();
                    } else {
                        $nuevaFecha = $fechaActual->addMonth();
                    }
                    Prestamos::where('id', $row->id)->update([
                        'proximo_pago' => $nuevaFecha->toDateString(),
                        'fecha_ultimo_descuento' => $request->fecha_ultimo_descuento
                    ]);
                }
            }

            \DB::commit();
            return redirect()
                ->route('admin.pagar.prestamo.index')
                ->with([
                    'id' => $socioID,
                    'prestamos_ids' => $prestamosIds
                ]);
        } catch (Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            return json_encode($query);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            
            $resultado = $this->procesarPagoPrestamos($request);

            if (!$resultado['ok']) {
                DB::rollBack();

                return redirect()->back()
                    ->with('error', $resultado['mensaje'])
                    ->withInput();
            }

            DB::commit();

            return redirect()
                ->route('admin.pagar.prestamo.index')
                ->with([
                    'id' => $resultado['socio_id'],
                    'prestamos_ids' => $resultado['prestamos_ids']
                ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function procesarPagoPrestamos(Request $request)
    {
        //try {

            $pagosIds = $request->prestamos_id ?? [];

            if (empty($pagosIds)) {
                return [
                    'ok' => false,
                    'redirect' => true,
                    'mensaje' => 'Debe seleccionar al menos un pago.'
                ];
            }

            $esAdelantado = count($pagosIds) > 1;

            $fechaUltimoDescuento = Carbon::createFromFormat(
                'Y-m-d',
                $request->fecha_ultimo_descuento
            )->startOfDay();

            /* ======================================================
            | VALIDACIÓN DE SERIES (SE QUEDA IGUAL)
            ====================================================== */

            $pagosSeleccionados = PagosPrestamos::whereIn('id', $pagosIds)
                ->orderBy('prestamos_id')
                ->orderBy('serie_pago')
                ->get()
                ->groupBy('prestamos_id');

            foreach ($pagosSeleccionados as $prestamoId => $pagosPrestamo) {

                $seriesSeleccionadas = $pagosPrestamo->pluck('serie_pago')->values();

                $seriesValidas = PagosPrestamos::where('prestamos_id', $prestamoId)
                    ->where('pagado', 0)
                    ->whereDate('fecha_tabla', '>', $fechaUltimoDescuento)
                    ->orderBy('serie_pago')
                    ->pluck('serie_pago')
                    ->values();

                if ($seriesValidas->isEmpty()) {
                    return [
                        'ok' => false,
                        'redirect' => true,
                        'mensaje' => "El préstamo {$prestamoId} no tiene pagos posteriores al último descuento."
                    ];
                }

                if ($seriesSeleccionadas->first() !== $seriesValidas->first()) {
                    return [
                        'ok' => false,
                        'redirect' => true,
                        'mensaje' => "El préstamo {$prestamoId} debe iniciar desde la serie {$seriesValidas->first()}."
                    ];
                }

                for ($i = 0; $i < $seriesSeleccionadas->count(); $i++) {
                    if (!isset($seriesValidas[$i]) || $seriesSeleccionadas[$i] !== $seriesValidas[$i]) {
                        return [
                            'ok' => false,
                            'redirect' => true,
                            'mensaje' => "Las series del préstamo {$prestamoId} deben ser consecutivas sin saltos."
                        ];
                    }
                }
            }

            /* ======================================================
            | TRANSACCIÓN
            ====================================================== */

            //\DB::beginTransaction();

            $socioID = $request->socios_id;
            $datosFormulario = $request->all();
            $prestamosIds = [];
            $formaPago = $request->forma_pago;
            $afectaSaldoSocio = !in_array($formaPago, [
                'LIQUIDAR PRÉSTAMO - REESTRUCTURACIÓN',
                'LIQUIDAR PRÉSTAMO - PAGO TOTAL'
            ]);

            $interesesPorPrestamo = [];

            if (!empty($pagosIds)) {
                foreach ($pagosIds as $pagoId) {

                    // Obtener el registro de pago exacto
                    $pago = PagosPrestamos::where('id', $pagoId)
                        ->where('pagado', 0)
                        ->firstOrFail();

                    // ACUMULAR INTERES POR PRÉSTAMO INDIVIDUAL
                    $prestamoId = $pago->prestamos_id;

                    if (!isset($interesesPorPrestamo[$prestamoId])) {
                        $interesesPorPrestamo[$prestamoId] = 0;
                    }

                    $interesesPorPrestamo[$prestamoId] += $pago->interes ?? 0;

                    // Guardas los IDs para enviarlos después
                    if ($pago->prestamos_id > 0) {
                        $prestamosIds[] = $pago->prestamos_id;
                    }

                    // Monto del pago
                    $pagoCapital = $pago->capital;
                    $idprestamoPago = $pago->id;

                    // Actualizar pago a pagado
                    $pago->update([
                        'pagado'       => 1,
                        'forma_pago'   => $request->forma_pago,
                        'metodo_pago'  => $request->metodo_pago,
                        'referencia'   => $request->referencia,
                        'fecha_pago'   => now(),
                        'fecha_captura'=> now(),
                        'es_adelantado' => $esAdelantado,
                        'es_reversion'  => 0,
                        'reversion_de'  => null,
                        'wci'          => auth()->user()->id,
                    ]);

                    // Obtener préstamo
                    $prestamo = Prestamos::findOrFail($pago->prestamos_id);

                    // ----------- Lógica de avales -----------
                    $avales = PrestamoDetalle::where('prestamos_id', $prestamo->id)
                        ->where('debe', '>', 0)
                        ->get();

                    if ($avales->count() > 0) {
                        // Distribuir entre avales
                        $totalAvales = $avales->count();
                        $abonoAval   = $pagoCapital / $totalAvales;
                        $sumaAbonosAval = 0;

                        foreach ($avales as $row) {
                            $rowPrestamo = Prestamos::findOrFail($row->prestamos_id);

                            $restanteAval = $row->debe;
                            $abonoReal = min($abonoAval, $restanteAval);

                            $sumaAbonosAval += $abonoReal;

                            // Guardar detalle de pago
                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id'       => $row->prestamos_id,
                                'socios_id'          => $row->socios_id,
                                'tipo_cliente'       => 'AVAL',
                                'abona'              => $abonoReal,
                                'es_adelantado'      => $esAdelantado,
                                'es_reversion'       => 0,
                                'reversion_de'       => null,
                                'wci'                => auth()->user()->id,
                            ]);

                            // Actualizar préstamo del aval
                            $rowPrestamo->increment('abona', $abonoReal);
                            $rowPrestamo->decrement('debe', $abonoReal);
                            $rowPrestamo->update([
                                'fecha_pago_reestructuracion' => $request->fecha_ultimo_descuento,
                                'monto_pago_reestructuracion' => $pagoCapital,
                            ]);

                            // Actualizar socio aval
                            $aval = Socios::find($row->socios_id);

                            $movimiento = $prestamo->movimientos()->create([
                                'socios_id'       => $row->socios_id,
                                'fecha'           => now(),
                                'folio'           => 'MOV-',
                                'saldo_anterior'  => $aval->saldo,
                                'saldo_actual'    => $aval->saldo,
                                'monto'           => $abonoReal,
                                'movimiento'      => 'PAGO PRÉSTAMO -AVAL-',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago'     => 'EFECTIVO',
                                'estatus'         => 'EFECTUADO',
                            ]);

                            $movimiento->update([
                                'folio' => 'MOV-' . $movimiento->id,
                            ]);

                            $aval->decrement('monto_prestamos', $abonoReal);

                            // Actualizar detalle aval
                            $row->increment('abona', $abonoReal);
                            $row->decrement('debe', $abonoReal);
                            $row->update([
                                'fecha_pago_reestructuracion' =>$request->fecha_ultimo_descuento,
                                'monto_pago_reestructuracion' => $abonoReal
                            ]);
                        }

                        // Lo que sobra para el socio principal
                        $capitalRestante = $pagoCapital - $sumaAbonosAval;
                        if ($capitalRestante > 0) {
                            $interes = $prestamo->total_intereses / $prestamo->total_quincenas;

                            $prestamo->increment('abona', $capitalRestante);
                            $prestamo->decrement('debe', $capitalRestante);
                            $prestamo->update([
                                'fecha_pago_reestructuracion' => $request->fecha_ultimo_descuento,
                                'monto_pago_reestructuracion' => $pagoCapital,
                            ]);

                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id'       => $prestamo->id,
                                'socios_id'          => $prestamo->socios_id,
                                'tipo_cliente'       => 'SOCIO',
                                'abona'              => $capitalRestante, //$capitalRestante + $interes,
                                'es_adelantado'      => $esAdelantado,
                                'es_reversion'       => 0,
                                'reversion_de'       => null,
                                'wci'                => auth()->user()->id,
                            ]);

                            // Movimiento del socio
                            $socio = Socios::find($prestamo->socios_id);

                            $movimiento = $prestamo->movimientos()->create([
                                'socios_id'       => $socio->id,
                                'fecha'           => now(),
                                'folio'           => 'MOV-',
                                'saldo_anterior'  => $socio->saldo,
                                'saldo_actual'    => $socio->saldo - ($capitalRestante), // $socio->saldo - ($capitalRestante + $interes),
                                'monto'           => $capitalRestante, //$capitalRestante + $interes,
                                'movimiento'      => 'PAGO PRÉSTAMO -AVAL-',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago'     => 'EFECTIVO',
                                'estatus'         => 'EFECTUADO',
                            ]);

                            $movimiento->update([
                                'folio' => 'MOV-' . $movimiento->id,
                            ]);

                            $socio->decrement('monto_prestamos', $capitalRestante);
                        }
                    } else {
                        // ----------- Sin avales -----------
                        $abonoReal = min($pagoCapital, $prestamo->debe);

                        $prestamo->increment('abona', $abonoReal);
                        $prestamo->decrement('debe', $abonoReal);
                        $prestamo->update([
                            'fecha_pago_reestructuracion' => $request->fecha_ultimo_descuento,
                            'monto_pago_reestructuracion' => $pagoCapital,
                        ]);

                        PagosPrestamosDetalles::create([
                            'pagos_prestamos_id' => $idprestamoPago,
                            'prestamos_id'       => $prestamo->id,
                            'socios_id'          => $prestamo->socios_id,
                            'tipo_cliente'       => 'SOCIO',
                            'abona'              => $abonoReal,
                            'es_adelantado'      => $esAdelantado,
                            'es_reversion'       => 0,
                            'reversion_de'       => null,
                            'wci'                => auth()->user()->id,
                        ]);

                        $socio = Socios::find($prestamo->socios_id);
                        $lastId = Movimiento::max('id') ?? 0;
                        $saldoActual = $socio->saldo;
                        if ($afectaSaldoSocio) {
                            $saldoActual = $socio->saldo - $abonoReal;
                        }

                        $movimiento = $prestamo->movimientos()->create([
                            'socios_id'       => $socio->id,
                            'fecha'           => now(),
                            'folio'           => 'MOV-',
                            'saldo_anterior'  => $socio->saldo,
                            'saldo_actual'    => $saldoActual,
                            'monto'           => $abonoReal,
                            'movimiento'      => 'PAGO PRÉSTAMO',
                            'tipo_movimiento' => 'ABONO',
                            'metodo_pago'     => 'EFECTIVO',
                            'estatus'         => 'EFECTUADO',
                        ]);

                        $movimiento->update([
                            'folio' => 'MOV-' . $movimiento->id,
                        ]);

                        $socio->decrement('monto_prestamos', $abonoReal);

                        //VALIDA SI ES POR PAGO CON SALDO DEL SOCIO 'LIQUIDACIÓN DE PRÉSTAMO - REESTRUCTURACIÓN',
                        if ($afectaSaldoSocio) {
                            $socio->decrement('saldo', $abonoReal);
                        }
                    }
                }

                // CONDONACIÓN DE INTERESES
                foreach ($interesesPorPrestamo as $prestamoId => $interesCondonado) {

                    if ($interesCondonado <= 0) {
                        continue;
                    }

                    $prestamo = Prestamos::find($prestamoId);
                    if (!$prestamo) {
                        continue;
                    }

                    $socio = Socios::find($prestamo->socios_id);
                    $lastId = Movimiento::max('id') ?? 0;

                    // 🔹 Ajuste contable del préstamo
                    $prestamo->increment('abona', $interesCondonado);
                    $prestamo->decrement('debe', $interesCondonado);

                    // 🔹 Movimiento contable (NO afecta saldo)
                    $movimiento = $prestamo->movimientos()->create([
                        'socios_id'       => $prestamo->socios_id,
                        'fecha'           => now(),
                        'folio'           => 'MOV-',
                        'saldo_anterior'  => $socio->saldo,
                        'saldo_actual'    => $socio->saldo,
                        'monto'           => $interesCondonado,
                        'movimiento'      => 'CONDONACIÓN DE INTERESES POR ADELANTO',
                        'tipo_movimiento' => 'AJUSTE',
                        'metodo_pago'     => 'SISTEMA',
                        'estatus'         => 'EFECTUADO',
                    ]);

                    $movimiento->update([
                        'folio' => 'MOV-' . $movimiento->id,
                    ]);
                }

                // Eliminar IDs duplicados
                $prestamosIds = array_unique($prestamosIds);

                foreach ($prestamosIds as $id) {

                    $liquidado = $this->verificarFinPrestamo($id);

                    // 🟡 Solo si NO se liquidó, actualizar serie real
                    if (!$liquidado) {

                        $ultimaSeriePagada = PagosPrestamos::where('prestamos_id', $id)
                            ->where('pagado', 1)
                            ->max('serie_pago');

                        if (!is_null($ultimaSeriePagada)) {
                            Prestamos::where('id', $id)->update([
                                'serie' => $ultimaSeriePagada
                            ]);
                        }
                    }
                }

                // Actualizar próxima fecha de pago
                $fechaComparar = now()->toDateString();
                $prestamoFecha = Prestamos::join('pagos_prestamos', 'pagos_prestamos.prestamos_id', '=', 'prestamos.id')
                    ->whereDate('pagos_prestamos.fecha_captura', $fechaComparar)
                    ->select('pagos_prestamos.fecha_captura', 'prestamos.proximo_pago', 'prestamos.id')
                    ->get();

                foreach ($prestamoFecha as $row) {
                    $fechaActual = Carbon::parse($row->proximo_pago);
                    if ($fechaActual->isLastOfMonth()) {
                        $nuevaFecha = $fechaActual->addDays(15);
                    } elseif ($fechaActual->day == 15) {
                        $nuevaFecha = $fechaActual->endOfMonth();
                    } else {
                        $nuevaFecha = $fechaActual->addMonth();
                    }
                    Prestamos::where('id', $row->id)->update([
                        'proximo_pago' => $nuevaFecha->toDateString(),
                        'fecha_ultimo_descuento' => $request->fecha_ultimo_descuento
                    ]);
                }
            }

            //\DB::commit();

            return [
                'ok' => true,
                'socio_id' => $request->socios_id,
                'prestamos_ids' => $prestamosIds
            ];

        //} catch (\Exception $e) {
        //    \DB::rollBack();

        //    return [
        //        'ok' => false,
        //        'redirect' => false,
        //        'mensaje' => $e->getMessage()
        //    ];
        //}
    }

    private function revertirPagoAdelantado(array $pagosIds)
    {
        //try {

            //\DB::beginTransaction();

            // 🔍 Pagos adelantados ya efectuados y no revertidos
            $pagos = PagosPrestamos::whereIn('prestamos_id', function ($q) use ($pagosIds) {
                $q->select('prestamos_id')
                ->from('pagos_prestamos')
                ->whereIn('id', $pagosIds);
            })
            ->where('pagado', 1)
            ->where('es_adelantado', 1)
            ->where('es_reversion', 0)
            ->whereNull('reversion_de')
            ->orderBy('prestamos_id')
            ->orderByDesc('serie_pago') // 🔥 importante
            ->get();

            $capitalPorPrestamo = $pagos->sum('capital');
            $capitalInteresPrestamo = $pagos->sum('interes');
            $avalesPorPrestamo  = 0;
            $montoAnteriorSocio = null;
            $socioPrincipal     = null;

            $incrementoPrestamoAplicado = [];
            $incrementoAvalAplicado     = [];

            if ($pagos->isEmpty()) {
                return [
                    'ok' => false,
                    'mensaje' => 'No se encontraron pagos adelantados válidos para revertir.'
                ];
            }
            
            foreach ($pagos as $pago) {

                // 🔎 Detalles del pago (SOCIO + AVALES)
                $detalles = PagosPrestamosDetalles::where('pagos_prestamos_id', $pago->id)
                    ->where('es_reversion', 0)
                    ->get();

                foreach ($detalles as $detalle) {

                    $prestamo = Prestamos::lockForUpdate()->find($detalle->prestamos_id);
                    $socio    = Socios::lockForUpdate()->find($detalle->socios_id);

                    if (!$prestamo || !$socio) {
                        throw new \Exception('No se pudo localizar el préstamo o socio para la reversión.');
                    }

                    /* ======================================================
                    | 🔙 REVERSIÓN CONTABLE
                    ====================================================== */

                    // 🔹 GUARDAR MONTO ANTERIOR SOLO UNA VEZ Y SOLO PARA SOCIO
                    if ($detalle->tipo_cliente === 'SOCIO' && $montoAnteriorSocio === null) {
                        $montoAnteriorSocio = $socio->monto_prestamos;
                    }

                    // Revertir préstamo
                    $prestamo->decrement('abona', $detalle->abona);
                    $prestamo->increment('debe', $detalle->abona);

                    // Revertir socio / aval
                    $socio->increment('monto_prestamos', $detalle->abona);

                    if ($detalle->tipo_cliente === 'SOCIO') {
                        // 🔼 Regresa préstamo activo al socio
                        /*$socio->increment('numero_prestamos');
                        $socioPrincipal     = $socio;*/

                        $socioPrincipal = $socio;

                        if (!isset($incrementoPrestamoAplicado[$socio->id])) {
                            $incrementoPrestamoAplicado[$socio->id] = true;
                        }

                    } elseif ($detalle->tipo_cliente === 'AVAL') {
                        // 🔼 Regresa responsabilidad como aval
                        /*$avalesPorPrestamo += $detalle->abona;
                        $socio->increment('is_aval');*/
                        $avalesPorPrestamo += $detalle->abona;

                        if (!isset($incrementoAvalAplicado[$socio->id])) {
                            $incrementoAvalAplicado[$socio->id] = true;
                        }
                    }

                    /* ======================================================
                    | 📒 MOVIMIENTO CONTABLE INVERSO
                    ====================================================== */
                    $movimiento = $prestamo->movimientos()->create([
                        'socios_id'       => $detalle->socios_id,
                        'fecha'           => now(),
                        'folio'           => 'MOV-',
                        'saldo_anterior'  => $socio->saldo,
                        'saldo_actual'    => $socio->saldo,
                        'monto'           => $detalle->abona,
                        'movimiento'      => 'REVERSIÓN PAGO ADELANTADO',
                        'tipo_movimiento' => 'AJUSTE',
                        'metodo_pago'     => 'SISTEMA',
                        'estatus'         => 'EFECTUADO',
                    ]);

                    $movimiento->update([
                        'folio' => 'MOV-' . $movimiento->id,
                    ]);

                    // Marcar detalle como revertido
                    $detalle->update([
                        'es_reversion' => 1,
                        'reversion_de' => $pago->id,
                    ]);
                }

                /* ======================================================
                | 🔄 REVERSIÓN DEL PAGO PRINCIPAL
                ====================================================== */

                $pago->update([
                    'pagado'        => 0,
                    'referencia'    => null,
                    'metodo_pago'   => null,
                    'forma_pago'    => null,
                    'fecha_pago'    => null,
                    'fecha_captura' => null,
                    'fecha_pago'    => null,
                    'es_adelantado' => 0,
                    'es_reversion'  => 1,
                ]);

                /* ======================================================
                | 🔁 ACTUALIZAR SERIE REAL DEL PRÉSTAMO
                ====================================================== */

                $ultimaSeriePagada = PagosPrestamos::where('prestamos_id', $pago->prestamos_id)
                    ->where('pagado', 1)
                    ->max('serie_pago');

                Prestamos::where('id', $pago->prestamos_id)->update([
                    'serie' => $ultimaSeriePagada ?? 0
                ]);
            }

            // ACTUALIZO PRESTAMOS, SE CONSIDERA LOS INTERESES
            $actualiza = Prestamos::lockForUpdate()->find($pagos[0]->prestamos_id);
            $actualiza->monto_pago_reestructuracion =  null;
            $actualiza->fecha_pago_reestructuracion =  null;
            $actualiza->decrement('abona', $capitalInteresPrestamo);
            $actualiza->increment('debe', $capitalInteresPrestamo);

            // 🔼 Incrementar número de préstamos (una sola vez por socio)
            foreach (array_keys($incrementoPrestamoAplicado) as $socioId) {
                Socios::where('id', $socioId)->increment('numero_prestamos');
            }

            // 🔼 Incrementar is_aval (una sola vez por socio)
            foreach (array_keys($incrementoAvalAplicado) as $socioId) {
                Socios::where('id', $socioId)->increment('is_aval');
            }

            if ($socioPrincipal) {
                // fórmula solicitada:
                // (capital - avales) + monto anterior
                $nuevoMontoPrestamos =
                    ($capitalPorPrestamo - $avalesPorPrestamo) + $montoAnteriorSocio;

                $socioPrincipal->update([
                    'monto_prestamos' => $nuevoMontoPrestamos
                ]);
            }

            //\DB::commit();

            return [
                'ok' => true,
                'mensaje' => 'Pago adelantado revertido correctamente.'
            ];

        //} catch (\Throwable $e) {

        //    \DB::rollBack();

        //    return [
        //        'ok' => false,
        //        'mensaje' => 'Error al revertir el pago adelantado: ' . $e->getMessage()
        //    ];
        //}
    }

    private function verificarFinPrestamo(int $prestamoId): bool
    {
        $prestamo = Prestamos::find($prestamoId);

        if (!$prestamo) {
            return false;
        }

        // 🔍 Validar que la última serie esté pagada
        $ultimaSeriePagada = PagosPrestamos::where('prestamos_id', $prestamoId)
            ->where('serie_pago', $prestamo->total_quincenas)
            ->where('pagado', 1)
            ->exists();

        if (!$ultimaSeriePagada) {
            return false;
        }

        // 🔒 Cierre contable
        $prestamo->update([
            //'abona' => $prestamo->debia,
            //'debe'  => 0,
            'serie' => $prestamo->total_quincenas,
        ]);

        // ===============================
        // 👤 SOCIO PRINCIPAL
        // ===============================
        $socioPrincipal = Socios::find($prestamo->socios_id);

        if ($socioPrincipal && $socioPrincipal->numero_prestamos > 0) {
            $socioPrincipal->decrement('numero_prestamos');
        }

        // ===============================
        // 🤝 AVALES (DESDE PrestamoDetalle)
        // ===============================
        $avales = PrestamoDetalle::where('prestamos_id', $prestamoId)
            ->select('socios_id')
            ->distinct()
            ->get();

        foreach ($avales as $aval) {
            $avalSocio = Socios::find($aval->socios_id);

            if ($avalSocio && $avalSocio->is_aval > 0) {
                $avalSocio->decrement('is_aval');
            }
        }

        return true; // ✅ préstamo totalmente liquidado
    }

    private function verificarFinPrestamoNO($prestamoId): bool
    {
        $prestamo = Prestamos::find($prestamoId);

        if (!$prestamo) {
            return false;
        }

        // 🔍 ¿Existen pagos pendientes?
        $tienePagosPendientes = PagosPrestamos::where('prestamos_id', $prestamoId)
            ->where('pagado', 0)
            ->exists();

        if ($tienePagosPendientes) {
            return false; // ❌ aún no termina
        }

        // 🔒 CIERRE CONTABLE
        $prestamo->update([
            'abona' => $prestamo->debia,
            'debe'  => 0,
            'serie' => $prestamo->total_quincenas,
        ]);

        // 🔄 Ajuste de contadores del socio
        if ($prestamo->is_aval == 1) {
            Socios::where('id', $prestamo->socios_id)
                ->decrement('is_aval');
        } else {
            Socios::where('id', $prestamo->socios_id)
                ->decrement('numero_prestamos');
        }

        return true; // ✅ préstamo liquidado
    }

    public function store2(Request $request)
    {
        try {
            \DB::beginTransaction();
            $socioID = $request->socios_id;
            $datosFormulario = $request->all();
            $prestamosIds = [];

            if (isset($datosFormulario['prestamos_id']) && count($datosFormulario['prestamos_id']) > 0) {
                foreach ($request->prestamos_id as $key => $value) {
                   /* $prestamo = Prestamos::findorfail($value);
                    $prestamoPago = new PagosPrestamos();
                    $prestamoPago->prestamos_id = $request->prestamos_id[$key];
                    $prestamoPago->socios_id = $request->socios_id;
                    $prestamoPago->fecha_pago = Carbon::now();
                    $prestamoPago->fecha_captura = Carbon::now();
                    $prestamoPago->serie_pago = $prestamo->total_quincenas;
                    $prestamoPago->serie_final = $prestamo->total_quincenas;
                    $prestamoPago->importe = $prestamo->debia;
                    $prestamoPago->forma_pago = $request->forma_pago;
                    $prestamoPago->referencia = $request->referencia;
                    $prestamoPago->wci = auth()->user()->id;
                    $prestamoPago->save();
                    $idprestamoPago = $prestamoPago->id;*/

                    // Guardas los IDs para enviarlos después
                    if ($value > 0) {
                        $prestamosIds[] = $value;
                    }

                     //BUSCAMOS EL REGISTRO PARA REALIZAR EL ABONO
                    $prestamoPago = PagosPrestamos::where('prestamos_id', '=', $request->prestamos_id[$key])
                    ->where('pagado', 0)
                    ->get();

                    $pagoCapital = $prestamoPago->sum('capital');

                    $idprestamoPago = 0;
                    foreach ($prestamoPago as $pago) {
                        $idprestamoPago = $pago->id;
                        $pago->update([
                            'pagado' => 1,
                            'forma_pago' => $request->forma_pago,
                            'metodo_pago' => $request->metodo_pago,
                            'referencia' => $request->referencia,
                            'fecha_pago' => Carbon::now(),
                            'fecha_captura' => Carbon::now(),
                            'wci' => auth()->user()->id,
                        ]);
                    }

                    // BUSCAMOS SI EL PRESTAMO TIENE AVALES
                    $avales = PrestamoDetalle::where('prestamos_id', '=', $request->prestamos_id[$key])
                        ->where('debe', '>', 0)
                        //->whereRaw('debe > 0')
                        ->get(['prestamo_detalles.*']);

                    if ($avales->count() > 0) {
                        $totalAvales = $avales->count();
                        $abonoAval = $pagoCapital / $totalAvales;

                        $sumaAbonosAval = 0;
                        foreach ($avales as $row) {
                            //ABONAMOS AL AVAL
                            $rowPrestamo = Prestamos::findorfail($row->prestamos_id);

                            // Restante disponible para abonar al aval
                            $restanteAval = $row->debe;

                            // Calcula el abono real al aval
                            $abonoReal = min($abonoAval, $restanteAval);


                            // Acumula la suma de abonos a los avales
                            $sumaAbonosAval += $abonoReal;

                            // CREAMOS EL DETALLE DEL PRESTAMO-ABONO
                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id' => $row->prestamos_id,
                                'socios_id' => $row->socios_id,
                                'tipo_cliente' => 'AVAL',
                                'abona' => $abonoReal,
                                'wci' => auth()->user()->id,
                            ]);

                            // ACTUALIZAMOS LOS VALORES DEL PRESTAMO
                            $rowPrestamo->abona = $rowPrestamo->abona + $abonoReal; // Suma el abono al 'abona' existente
                            $rowPrestamo->debe = $rowPrestamo->debe - $abonoReal; // Resta el abono de 'debe'
                            $rowPrestamo->fecha_pago_reestructuracion = $request->fecha_ultimo_descuento; //Carbon::now();
                            $rowPrestamo->monto_pago_reestructuracion = $pagoCapital;
                            $rowPrestamo->save();

                            // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                            $aval = Socios::find($row->socios_id);

                            // INSERTAMOS EL MOVIMIENTO
                            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                            $nextId = $lastInsertedId + 1;
                            Movimiento::create([
                                'socios_id' => $row->socios_id,
                                'fecha' => Carbon::now(),
                                'folio' => 'MOV-' . $nextId,
                                'saldo_anterior' => $aval->saldo,
                                'saldo_actual' => $aval->saldo,
                                'monto' => $abonoReal,
                                'movimiento' => 'PAGO PRÉSTAMO',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago' => 'EFECTIVO',
                                'estatus' => 'EFECTUADO',
                            ]);

                            $aval->monto_prestamos = $aval->monto_prestamos - $abonoReal;
                            $aval->save();

                            // ACTUALIZAMOS PRESTAMOS_DETALLES DEL AVAL
                            $row->abona = $row->abona + $abonoReal; // Suma el abono al 'abona' existente
                            $row->debe = $row->debe - $abonoReal; // Resta el abono de 'debe'
                            $row->save();

                            // ACTUALIZAMOS SI ES AVAL
                            $avalDetalle = PrestamoDetalle::find($row->id);
                            if ($avalDetalle->debe == 0) {
                                $aval_socio = Socios::find($avalDetalle->socios_id);
                                if ($aval_socio) {
                                    $aval_socio->update([
                                        'is_aval' => $aval_socio->is_aval - 1,
                                    ]);
                                }
                            }

                            //actualiza en PrestamoDetalle el campo de el pago adelantado
                            $avalDetalle->update([
                                'fecha_pago_reestructuracion' => $request->fecha_ultimo_descuento, //Carbon::now(),
                                'monto_pago_reestructuracion' => $abonoReal
                            ]);
                        }

                        // ABONO DEL CLIENTE CON AVAL PERO CON EL RESTO DEL ABONO
                        // Calcula el capital restante después de los abonos a los avales
                        $capitalRestante = $pagoCapital - $sumaAbonosAval;

                        if ($capitalRestante > 0) {
                            $rowPrestamo = Prestamos::findorfail($request->prestamos_id[$key]);
                            $interes = $rowPrestamo->total_intereses / $rowPrestamo->total_quincenas;

                            // ACTUALIZAMOS LOS VALORES DEL PRESTAMO
                            $rowPrestamo->abona = $rowPrestamo->abona + $capitalRestante; // Suma el abono al 'abona' existente
                            $rowPrestamo->debe = $rowPrestamo->debe - $capitalRestante; // Resta el abono de 'debe'
                            $rowPrestamo->save();

                            // ACTUALIZAMOS AL SOSCIO SI TIENE PRESTAMO
                            $socioDetalle = Prestamos::find($rowPrestamo->id);
                            if ($socioDetalle->debe == 0) {
                                $socio_socio = Socios::find($socioDetalle->socios_id);
                                if ($socio_socio) {
                                    $socio_socio->update([
                                        'numero_prestamos' => $socio_socio->numero_prestamos - 1,
                                    ]);
                                }
                            }

                            $capitalRestante = $capitalRestante - $interes;
                            //dd($capitalRestante, $interes);
                            if ($capitalRestante > 0) {
                                PagosPrestamosDetalles::create([
                                    'pagos_prestamos_id' => $idprestamoPago,
                                    'prestamos_id' => $request->prestamos_id[$key],
                                    'socios_id' => $request->socios_id,
                                    'tipo_cliente' => 'SOCIO',
                                    //'abona' => $capitalRestante,
                                    'abona' => $capitalRestante + $interes,
                                    'wci' => auth()->user()->id,
                                ]);

                                // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                                $socio = Socios::find($request->socios_id);
                                // INSERTAMOS EL MOVIMIENTO
                                $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                                $nextId = $lastInsertedId + 1;
                                Movimiento::create([
                                    'socios_id' => $request->socios_id,
                                    'fecha' => Carbon::now(),
                                    'folio' => 'MOV-' . $nextId,
                                    'saldo_anterior' => $socio->saldo,
                                    'saldo_actual' => ($socio->saldo - $pagoCapital),
                                    //'monto' => $capitalRestante,
                                    'monto' => $capitalRestante + $interes,
                                    'movimiento' => 'PAGO PRÉSTAMO',
                                    'tipo_movimiento' => 'ABONO',
                                    'metodo_pago' => 'EFECTIVO',
                                    'estatus' => 'EFECTUADO',
                                ]);

                                //$socio->monto_prestamos = $socio->monto_prestamos - $capitalRestante;
                                $socio->monto_prestamos = $socio->monto_prestamos - ($capitalRestante + $interes );
                                //dd($capitalRestante, $interes);
                                $socio->save();
                            }
                        }
                    } else {
                        // ABONO DEL CLIENTE SIN AVAL
                        $abonoAval = $pagoCapital;
                        $rowPrestamo = Prestamos::findorfail($request->prestamos_id[$key]);
                        $interes = $rowPrestamo->total_intereses / $rowPrestamo->total_quincenas;

                        // Restante disponible para abonar al aval
                        $restanteAval = $rowPrestamo->debe;

                        // Calcula el abono real al aval
                        $abonoReal = min($abonoAval, $restanteAval);

                        // ACTUALIZAMOS LOS VALORES DEL PRESTAMO
                        $rowPrestamo->abona = $rowPrestamo->abona + $abonoReal; // Suma el abono al 'abona' existente
                        $rowPrestamo->debe = 0 ;//$rowPrestamo->debe - $abonoReal; // Resta el abono de 'debe'
                        $rowPrestamo->fecha_pago_reestructuracion = $request->fecha_ultimo_descuento; //Carbon::now() ;
                        $rowPrestamo->monto_pago_reestructuracion = $pagoCapital;
                        $rowPrestamo->save();

                        // ACTUALIZAMOS AL SOSCIO SI TIENE PRESTAMO
                        $socioDetalle = Prestamos::find($rowPrestamo->id);
                        if ($socioDetalle->debe == 0) {
                            $socio_socio = Socios::find($socioDetalle->socios_id);
                            if ($socio_socio) {
                                $socio_socio->update([
                                    'numero_prestamos' => $socio_socio->numero_prestamos - 1,
                                ]);
                            }
                        }

                        //$abonoReal = $abonoReal - $interes;
                        // CREAMOS EL DETALLE DEL PRESTAMO-ABONO
                        if ($abonoReal > 0) {
                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id' => $request->prestamos_id[$key],
                                'socios_id' => $rowPrestamo->socios_id,
                                'tipo_cliente' => 'SOCIO',
                                'abona' => $abonoReal,
                                'wci' => auth()->user()->id,
                            ]);

                            // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                            $socio = Socios::find($rowPrestamo->socios_id);
                            // INSERTAMOS EL MOVIMIENTO
                            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                            $nextId = $lastInsertedId + 1;
                            Movimiento::create([
                                'socios_id' => $request->socios_id,
                                'fecha' => Carbon::now(),
                                'folio' => 'MOV-' . $nextId,
                                'saldo_anterior' => $socio->saldo,
                                'saldo_actual' => ($socio->saldo - $pagoCapital),
                                'monto' => $abonoReal,
                                'movimiento' => 'PAGO PRÉSTAMO',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago' => 'EFECTIVO',
                                'estatus' => 'EFECTUADO',
                            ]);
                            $socio->monto_prestamos = $socio->monto_prestamos - $abonoReal;
                            $socio->saldo = $socio->saldo - $abonoReal;
                            $socio->save();
                        }
                    }
                }

                // ACTUALIZAMOS LA SERIE DEL PRESTAMO
                foreach ($request->prestamos_id as $key => $value) {
                    $prestamoSerie = Prestamos::findorfail($request->prestamos_id[$key]);
                    $prestamoSerie->update([
                        'serie' => $prestamoSerie->total_quincenas
                    ]);
                }

                // ACTUALIZAMOS LA PRÓXIMA FECHA DE PAGO DE LOS PRÉSTAMOS
                // Obtén la fecha actual del campo $rowPrestamo->proximo_pago
                $fechaComparar = now()->toDateString();
                $prestamoFecha = Prestamos::join('pagos_prestamos', 'pagos_prestamos.prestamos_id', '=', 'prestamos.id')
                    ->whereDate('pagos_prestamos.fecha_captura', $fechaComparar)
                    ->select('pagos_prestamos.fecha_captura', 'prestamos.proximo_pago', 'prestamos.id')
                    ->get();
                foreach ($prestamoFecha as $row) {
                    $fechaActual = Carbon::parse($row->proximo_pago);
                    // Verifica si la fecha actual es el último día del mes
                    if ($fechaActual->isLastOfMonth()) {
                        // La fecha actual es el último día del mes, así que calcula la nueva fecha como el día 15 del próximo mes
                        //$nuevaFecha = $fechaActual->addMonth()->startOfMonth()->addDays(14);
                        $nuevaFecha = $fechaActual->addDays(15);
                    } elseif ($fechaActual->day == 15) {
                        // La fecha actual es el día 15 de algún mes, así que calcula la nueva fecha como el último día del próximo mes
                        //$nuevaFecha = $fechaActual->addMonth()->endOfMonth();
                        $nuevaFecha = $fechaActual->endOfMonth();
                    } else {
                        // En otro caso, simplemente suma un mes a la fecha actual
                        $nuevaFecha = $fechaActual->addMonth();
                    }
                    $nuevaFecha = $nuevaFecha->toDateString();
                    //dd($request->fecha_ultimo_descuento, $nuevaFecha);
                    Prestamos::where('id', $row->id)->update([
                        'proximo_pago' => $nuevaFecha,
                        'fecha_ultimo_descuento' => $request->fecha_ultimo_descuento
                    ]);
                }
            }
            \DB::commit();
            //return redirect()->route('admin.socios.index')->with(['correcto' => 'success']);
            return redirect()
                ->route('admin.pagar.prestamo.index')
                ->with([
                    'id' => $socioID,
                    'prestamos_ids' => $prestamosIds
                ]);
        } catch (Exception $e) {
            \DB::rollback();
            dd($e);
            $query = $e->getMessage();
            return json_encode($query);
            return redirect()->back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function store1(Request $request)
    {
        try {
            \DB::beginTransaction();
            $socioID = $request->socios_id;
            $datosFormulario = $request->all();
            if (isset($datosFormulario['prestamos_id']) && count($datosFormulario['prestamos_id']) > 0) {
                foreach ($request->prestamos_id as $key => $value) {
                    $prestamo = Prestamos::findorfail($value);
                    $prestamoPago = new PagosPrestamos();
                    $prestamoPago->prestamos_id = $request->prestamos_id[$key];
                    $prestamoPago->socios_id = $request->socios_id;
                    $prestamoPago->fecha_pago = Carbon::now();
                    $prestamoPago->fecha_captura = Carbon::now();
                    $prestamoPago->serie_pago = $prestamo->total_quincenas;
                    $prestamoPago->serie_final = $prestamo->total_quincenas;
                    $prestamoPago->importe = $prestamo->debia;
                    $prestamoPago->forma_pago = $request->forma_pago;
                    $prestamoPago->referencia = $request->referencia;
                    $prestamoPago->wci = auth()->user()->id;
                    $prestamoPago->save();
                    $idprestamoPago = $prestamoPago->id;

                    // BUSCAMOS SI EL PRESTAMO TIENE AVALES
                    $avales = PrestamoDetalle::where('prestamos_id', '=', $request->prestamos_id[$key])
                        ->where('debe', '>', 0)
                        //->whereRaw('debe > 0')
                        ->get(['prestamo_detalles.*']);
                    if ($avales->count() > 0) {
                        $totalAvales = $avales->count();
                        $abonoAval = $prestamo->debia / $totalAvales;
                        $sumaAbonosAval = 0;
                        foreach ($avales as $row) {
                            //ABONAMOS AL AVAL
                            $rowPrestamo = Prestamos::findorfail($row->prestamos_id);

                            // Restante disponible para abonar al aval
                            $restanteAval = $row->debe;

                            // Calcula el abono real al aval
                            $abonoReal = min($abonoAval, $restanteAval);

                            // Acumula la suma de abonos a los avales
                            $sumaAbonosAval += $abonoReal;

                            // CREAMOS EL DETALLE DEL PRESTAMO-ABONO
                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id' => $row->prestamos_id,
                                'socios_id' => $row->socios_id,
                                'tipo_cliente' => 'AVAL',
                                'abona' => $abonoReal,
                                'wci' => auth()->user()->id,
                            ]);

                            // ACTUALIZAMOS LOS VALORES DEL PRESTAMO
                            $rowPrestamo->abona = $rowPrestamo->abona + $abonoReal; // Suma el abono al 'abona' existente
                            $rowPrestamo->debe = $rowPrestamo->debe - $abonoReal; // Resta el abono de 'debe'
                            $rowPrestamo->save();

                            // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                            $aval = Socios::find($row->socios_id);

                            // INSERTAMOS EL MOVIMIENTO
                            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                            $nextId = $lastInsertedId + 1;
                            Movimiento::create([
                                'socios_id' => $row->socios_id,
                                'fecha' => Carbon::now(),
                                'folio' => 'MOV-' . $nextId,
                                'saldo_anterior' => $aval->saldo,
                                'saldo_actual' => $aval->saldo,
                                'monto' => $abonoReal,
                                'movimiento' => 'PAGO PRÉSTAMO',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago' => 'EFECTIVO',
                                'estatus' => 'EFECTUADO',
                            ]);

                            $aval->monto_prestamos = $aval->monto_prestamos - $abonoReal;
                            $aval->save();

                            // ACTUALIZAMOS PRESTAMOS_DETALLES DEL AVAL
                            $row->abona = $row->abona + $abonoReal; // Suma el abono al 'abona' existente
                            $row->debe = $row->debe - $abonoReal; // Resta el abono de 'debe'
                            $row->save();

                            // ACTUALIZAMOS SI ES AVAL
                            $avalDetalle = PrestamoDetalle::find($row->id);
                            if ($avalDetalle->debe == 0) {
                                $aval_socio = Socios::find($avalDetalle->socios_id);
                                if ($aval_socio) {
                                    $aval_socio->update([
                                        'is_aval' => $aval_socio->is_aval - 1,
                                    ]);
                                }
                            }
                        }

                        // ABONO DEL CLIENTE CON AVAL PERO CON EL RESTO DEL ABONO
                        // Calcula el capital restante después de los abonos a los avales
                        $capitalRestante = $prestamo->debia - $sumaAbonosAval;
                        if ($capitalRestante > 0) {
                            $rowPrestamo = Prestamos::findorfail($request->prestamos_id[$key]);
                            $interes = $rowPrestamo->total_intereses / $rowPrestamo->total_quincenas;

                            // ACTUALIZAMOS LOS VALORES DEL PRESTAMO
                            $rowPrestamo->abona = $rowPrestamo->abona + $capitalRestante; // Suma el abono al 'abona' existente
                            $rowPrestamo->debe = $rowPrestamo->debe - $capitalRestante; // Resta el abono de 'debe'
                            $rowPrestamo->save();

                            // ACTUALIZAMOS AL SOSCIO SI TIENE PRESTAMO
                            $socioDetalle = Prestamos::find($rowPrestamo->id);
                            if ($socioDetalle->debe == 0) {
                                $socio_socio = Socios::find($socioDetalle->socios_id);
                                if ($socio_socio) {
                                    $socio_socio->update([
                                        'numero_prestamos' => $socio_socio->numero_prestamos - 1,
                                    ]);
                                }
                            }

                            $capitalRestante = $capitalRestante - $interes;
                            if ($capitalRestante > 0) {
                                PagosPrestamosDetalles::create([
                                    'pagos_prestamos_id' => $idprestamoPago,
                                    'prestamos_id' => $request->prestamos_id[$key],
                                    'socios_id' => $request->socios_id,
                                    'tipo_cliente' => 'SOCIO',
                                    'abona' => $capitalRestante,
                                    'wci' => auth()->user()->id,
                                ]);

                                // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                                $socio = Socios::find($request->socios_id);
                                // INSERTAMOS EL MOVIMIENTO
                                $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                                $nextId = $lastInsertedId + 1;
                                Movimiento::create([
                                    'socios_id' => $request->socios_id,
                                    'fecha' => Carbon::now(),
                                    'folio' => 'MOV-' . $nextId,
                                    'saldo_anterior' => $socio->saldo,
                                    'saldo_actual' => ($socio->saldo - $prestamo->debia),
                                    'monto' => $capitalRestante,
                                    'movimiento' => 'PAGO PRÉSTAMO',
                                    'tipo_movimiento' => 'ABONO',
                                    'metodo_pago' => 'EFECTIVO',
                                    'estatus' => 'EFECTUADO',
                                ]);
                                $socio->monto_prestamos = $socio->monto_prestamos - $capitalRestante;
                                $socio->save();
                            }
                        }
                    } else {
                        // ABONO DEL CLIENTE SIN AVAL
                        $abonoAval = $prestamo->debia;
                        $rowPrestamo = Prestamos::findorfail($request->prestamos_id[$key]);
                        $interes = $rowPrestamo->total_intereses / $rowPrestamo->total_quincenas;

                        // Restante disponible para abonar al aval
                        $restanteAval = $rowPrestamo->debe;

                        // Calcula el abono real al aval
                        $abonoReal = min($abonoAval, $restanteAval);

                        // ACTUALIZAMOS LOS VALORES DEL PRESTAMO
                        $rowPrestamo->abona = $rowPrestamo->abona + $abonoReal; // Suma el abono al 'abona' existente
                        $rowPrestamo->debe = $rowPrestamo->debe - $abonoReal; // Resta el abono de 'debe'
                        $rowPrestamo->save();

                        // ACTUALIZAMOS AL SOSCIO SI TIENE PRESTAMO
                        $socioDetalle = Prestamos::find($rowPrestamo->id);
                        if ($socioDetalle->debe == 0) {
                            $socio_socio = Socios::find($socioDetalle->socios_id);
                            if ($socio_socio) {
                                $socio_socio->update([
                                    'numero_prestamos' => $socio_socio->numero_prestamos - 1,
                                ]);
                            }
                        }

                        //$abonoReal = $abonoReal - $interes;
                        // CREAMOS EL DETALLE DEL PRESTAMO-ABONO
                        if ($abonoReal > 0) {
                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id' => $request->prestamos_id[$key],
                                'socios_id' => $rowPrestamo->socios_id,
                                'tipo_cliente' => 'SOCIO',
                                'abona' => $abonoReal,
                                'wci' => auth()->user()->id,
                            ]);

                            // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                            $socio = Socios::find($rowPrestamo->socios_id);
                            // INSERTAMOS EL MOVIMIENTO
                            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                            $nextId = $lastInsertedId + 1;
                            Movimiento::create([
                                'socios_id' => $request->socios_id,
                                'fecha' => Carbon::now(),
                                'folio' => 'MOV-' . $nextId,
                                'saldo_anterior' => $socio->saldo,
                                'saldo_actual' => ($socio->saldo - $prestamo->debia),
                                'monto' => $abonoReal,
                                'movimiento' => 'PAGO PRÉSTAMO',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago' => 'EFECTIVO',
                                'estatus' => 'EFECTUADO',
                            ]);
                            $socio->monto_prestamos = $socio->monto_prestamos - $abonoReal;
                            $socio->saldo = $socio->saldo - $abonoReal;
                            $socio->save();
                        }
                    }
                }

                // ACTUALIZAMOS LA SERIE DEL PRESTAMO
                foreach ($request->prestamos_id as $key => $value) {
                    $prestamoSerie = Prestamos::findorfail($request->prestamos_id[$key]);
                    $prestamoSerie->update([
                        'serie' => $prestamoSerie->total_quincenas
                    ]);
                }

                // ACTUALIZAMOS LA PRÓXIMA FECHA DE PAGO DE LOS PRÉSTAMOS
                // Obtén la fecha actual del campo $rowPrestamo->proximo_pago
                $fechaComparar = now()->toDateString();
                $prestamoFecha = Prestamos::join('pagos_prestamos', 'pagos_prestamos.prestamos_id', '=', 'prestamos.id')
                    ->whereDate('pagos_prestamos.fecha_captura', $fechaComparar)
                    ->select('pagos_prestamos.fecha_captura', 'prestamos.proximo_pago', 'prestamos.id')
                    ->get();
                foreach ($prestamoFecha as $row) {
                    $fechaActual = Carbon::parse($row->proximo_pago);
                    // Verifica si la fecha actual es el último día del mes
                    if ($fechaActual->isLastOfMonth()) {
                        // La fecha actual es el último día del mes, así que calcula la nueva fecha como el día 15 del próximo mes
                        //$nuevaFecha = $fechaActual->addMonth()->startOfMonth()->addDays(14);
                        $nuevaFecha = $fechaActual->addDays(15);
                    } elseif ($fechaActual->day == 15) {
                        // La fecha actual es el día 15 de algún mes, así que calcula la nueva fecha como el último día del próximo mes
                        //$nuevaFecha = $fechaActual->addMonth()->endOfMonth();
                        $nuevaFecha = $fechaActual->endOfMonth();
                    } else {
                        // En otro caso, simplemente suma un mes a la fecha actual
                        $nuevaFecha = $fechaActual->addMonth();
                    }
                    $nuevaFecha = $nuevaFecha->toDateString();
                    Prestamos::where('id', $row->id)->update([
                        'proximo_pago' => $nuevaFecha,
                    ]);
                }
            }
            \DB::commit();
            //return redirect()->route('admin.socios.index')->with(['correcto' => 'success']);
            return redirect()->route('admin.pagar.prestamo.index')->with(['id' => $socioID]);
        } catch (Exception $e) {
            \DB::rollback();
            dd($e);
            $query = $e->getMessage();
            return json_encode($query);
            return redirect()->back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show($id)
    {
        /*
        $prestamo = Prestamos::leftJoin('pagos_prestamos', 'prestamos.id', '=', 'pagos_prestamos.prestamos_id')
        ->where('prestamos.socios_id', $id)
        ->where('prestamos.debe', '>', 0)
        ->where('prestamos.estatus', 'AUTORIZADO')
        ->where('prestamos.prestamo_especial', 0)
        ->groupBy(
            'prestamos.id',
            'prestamos.socios_id',
            'prestamos.monto_prestamo',
            'prestamos.debe',
            'prestamos.fecha_prestamo'
        )
        ->orderBy('prestamos.fecha_prestamo', 'asc')
        ->selectRaw('
            prestamos.id,
            prestamos.socios_id,
            prestamos.fecha_prestamo,
            prestamos.monto_prestamo,
            prestamos.debe,

            ROUND(COALESCE(SUM(CASE WHEN pagos_prestamos.pagado = 1 THEN pagos_prestamos.decuento ELSE 0 END), 2), 2) AS total_abonado,
            ROUND(COALESCE(SUM(CASE WHEN pagos_prestamos.pagado = 0 THEN pagos_prestamos.capital ELSE 0 END), 2), 2) AS total_deuda
        ')
        ->get();
        */

        /*
        $prestamo = Prestamos::leftJoin('pagos_prestamos', function ($join) {
            $join->on('prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                ->where('pagos_prestamos.pagado', 0); // Solo pagos pendientes
        })
        ->where('prestamos.socios_id', $id)
        ->where('prestamos.debe', '>', 0)
        ->where('prestamos.estatus', 'AUTORIZADO')
        ->where('prestamos.prestamo_especial', 0)
        ->orderBy('prestamos.fecha_prestamo', 'asc')
        ->select(
            'prestamos.id as prestamo_id',
            'prestamos.socios_id',
            'prestamos.fecha_prestamo',
            'prestamos.monto_prestamo',
            'prestamos.debe',
            'pagos_prestamos.id as pago_id',
            'pagos_prestamos.fecha_pago',
            'pagos_prestamos.serie_pago',
            'pagos_prestamos.capital',
            'pagos_prestamos.interes',
            'pagos_prestamos.decuento',
            'pagos_prestamos.forma_pago',
            'pagos_prestamos.referencia'
        )
        ->get();

        // Generar numeración secuencial por préstamo
        $contador = 1;
        $ultimoPrestamo = null;

        foreach ($prestamo as $p) {
            if ($ultimoPrestamo !== $p->prestamo_id) {
                $ultimoPrestamo = $p->prestamo_id;
                $p->numero_prestamo = "Préstamo {$contador}";
                $contador++;
            } else {
                $p->numero_prestamo = "Préstamo " . ($contador - 1);
            }
        }
        */

        $prestamo = Prestamos::where('prestamos.socios_id', $id)
            ->where('prestamos.estatus', 'AUTORIZADO')
            ->where('prestamos.prestamo_especial', 0)
            ->whereExists(function($query) {
                // Solo incluir préstamos cuyo último pago aún no esté pagado
                $query->select(DB::raw(1))
                    ->from('pagos_prestamos as pp1')
                    ->whereColumn('pp1.prestamos_id', 'prestamos.id')
                    ->where('pp1.pagado', 0)
                    ->whereRaw('pp1.serie_pago = (SELECT MAX(pp2.serie_pago)
                                                    FROM pagos_prestamos as pp2
                                                    WHERE pp2.prestamos_id = pp1.prestamos_id)');
            })
            ->leftJoin('pagos_prestamos', function ($join) {
                $join->on('prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                    ->where('pagos_prestamos.pagado', 0); // Solo pagos pendientes
            })
            ->orderBy('pagos_prestamos.serie_pago', 'asc')
            ->select(
                'prestamos.id as prestamo_id',
                'prestamos.socios_id',
                'prestamos.fecha_prestamo',
                'prestamos.monto_prestamo',
                'prestamos.debe',
                'pagos_prestamos.id as pago_id',
                'pagos_prestamos.fecha_pago',
                'pagos_prestamos.serie_pago',
                'pagos_prestamos.capital',
                'pagos_prestamos.interes',
                'pagos_prestamos.decuento',
                'pagos_prestamos.forma_pago',
                'pagos_prestamos.referencia',
                'pagos_prestamos.fecha_tabla'
            )
            ->get();

        // Generar numeración secuencial por préstamo
        $contador = 1;
        $ultimoPrestamo = null;

        foreach ($prestamo as $p) {
            if ($ultimoPrestamo !== $p->prestamo_id) {
                $ultimoPrestamo = $p->prestamo_id;
                $p->numero_prestamo = "Préstamo {$contador}";
                $contador++;
            } else {
                $p->numero_prestamo = "Préstamo " . ($contador - 1);
            }
        }

        $socio = Socios::findorfail($prestamo[0]->socios_id);

        $tipoValues = ['LIQUIDAR PRÉSTAMO - REESTRUCTURACIÓN','LIQUIDAR PRÉSTAMO - PAGO TOTAL', 'LIQUIDAR PRÉSTAMO - TRASLADO DE AHORRO'];

        return view('pagar_prestamos.show', compact('prestamo', 'socio','tipoValues'));

    }

    public function edit($id)
    {
        $prestamo = Prestamos::findOrFail($id);

        $pagos = $prestamo->pagos()
        ->where(function ($q) {
            $q->where('pagado', 0)
            ->orWhere(function ($q2) {
                $q2->where('pagado', 1)
                    ->where('es_adelantado', 1);
            });
        })
        ->get();

        // Agregar campo virtual a cada pago
        foreach ($pagos as $pago) {
            $pago->numero_prestamo = 'Préstamo 1';
        }

        $socio = Socios::findorfail($prestamo->socios_id);

        $tipoValues = ['LIQUIDAR PRÉSTAMO - REESTRUCTURACIÓN','LIQUIDAR PRÉSTAMO - PAGO TOTAL', 'LIQUIDAR PRÉSTAMO - TRASLADO DE AHORRO'];

        return view('pagar_prestamos.edit', compact('prestamo', 'socio','pagos','tipoValues'));
    }

    public function update(Request $request, $prestamoId)
    {
        try { 
            \DB::beginTransaction();

            // 1️⃣ Obtener pagos adelantados activos
            $pagosIds = PagosPrestamos::where('prestamos_id', $prestamoId)
                ->where('pagado', 1)
                ->where('es_adelantado', 1)
                ->where('es_reversion', 0)
                ->pluck('id')
                ->toArray();

            // 2️⃣ Revertir pagos adelantados
            if (!empty($pagosIds)) {
                $resultadoReversion = $this->revertirPagoAdelantado($pagosIds);

                if (!$resultadoReversion['ok']) {
                    throw new \Exception($resultadoReversion['mensaje']);
                }
            }

            // 3️⃣ Aplicar nuevos pagos
            $resultado = $this->procesarPagoPrestamos($request);

            if (!$resultado['ok']) {
                throw new \Exception($resultado['mensaje']);
            }

            // 4️⃣ Confirmar transacción
            \DB::commit();

            return redirect()
                ->route('admin.pagar.prestamo.index')
                ->with([
                    'id' => $resultado['socio_id'],
                    'prestamos_ids' => $resultado['prestamos_ids']
                ]);
        } catch (\Throwable $e) {

            \DB::rollBack();
            dd($e->getMessage());

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($prestamoId)
    {
        try {
            \DB::beginTransaction();

            // 1️⃣ Pagos adelantados activos
            $pagosIds = PagosPrestamos::where('prestamos_id', $prestamoId)
                ->where('pagado', 1)
                ->where('es_adelantado', 1)
                ->where('es_reversion', 0)
                ->pluck('id')
                ->toArray();

            // 2️⃣ Revertir adelantos
            if (!empty($pagosIds)) {
                $resultado = $this->revertirPagoAdelantado($pagosIds);

                if (!$resultado['ok']) {
                    throw new \Exception($resultado['mensaje']);
                }
            }

            \DB::commit();

            return response()->json([
                'ok' => true,
                'mensaje' => 'El pago fue cancelado correctamente.'
            ]);

        } catch (\Throwable $e) {

            \DB::rollBack();

            return response()->json([
                'ok' => false,
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function reciboLiquidaPrestamo($id, Request $request)
    {
        //$prestamosIds = $request->prestamos_id ?? [];

        $prestamosIds = json_decode($request->prestamos_ids, true) ?? [];

        //dd($request->prestamos_ids, $prestamosIds);

        $liquido = Prestamos::leftJoin('pagos_prestamos', function ($join) {
            $join->on('prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                ->where('pagos_prestamos.pagado', '=', 1); // Solo pagos NO pagados
        })
        ->join('socios', 'prestamos.socios_id', '=', 'socios.id')
        ->where('prestamos.socios_id', $id)
        ->whereIn('prestamos.id', $prestamosIds)
        ->where('prestamos.estatus', 'AUTORIZADO')
        ->where('prestamos.prestamo_especial', 0)
        ->groupBy(
            'prestamos.id',
            'prestamos.socios_id',
            'prestamos.fecha_pago_reestructuracion',
            'prestamos.fecha_prestamo',
            'prestamos.monto_prestamo',
            'prestamos.fecha_ultimo_descuento',
            'prestamos.debe',
            'socios.num_socio',
            'socios.nombre_completo'
        )
        ->orderBy('prestamos.fecha_prestamo', 'asc')
        ->selectRaw('
            prestamos.id,
            prestamos.socios_id,
            prestamos.fecha_pago_reestructuracion,
            prestamos.fecha_prestamo,
            prestamos.monto_prestamo,
            prestamos.fecha_ultimo_descuento,
            prestamos.debe,
            socios.num_socio,
            socios.nombre_completo,
            MAX(pagos_prestamos.fecha_tabla) as ultima_fecha_tabla,
            -- Suma de capital donde forma_pago está vacío
            COALESCE(SUM(CASE
                WHEN (pagos_prestamos.forma_pago IS NULL OR pagos_prestamos.forma_pago = "")
                THEN pagos_prestamos.capital
                ELSE 0
            END), 0) as capital_sin_forma_pago,

            -- Suma de capital donde forma_pago tiene algún valor
            COALESCE(SUM(CASE
                WHEN (pagos_prestamos.forma_pago IS NOT NULL AND pagos_prestamos.forma_pago != "")
                THEN pagos_prestamos.capital
                ELSE 0
            END), 0) as capital_con_forma_pago,
            MAX(pagos_prestamos.referencia) as referencia
        ')
        ->get();

        $socio = Socios::findorfail($liquido[0]->socios_id);

        //  - CREAMOS EL PDF ----
        $pdf = PDF::loadView('recibos.recibo_liquido_prestamo', compact('liquido','socio'))
            ->setPaper('letter', 'portrait');
        return $pdf->stream();
    }
}
