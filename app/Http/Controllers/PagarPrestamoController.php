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

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $socioID = $request->socios_id;
            $datosFormulario = $request->all();
            $prestamosIds = [];

            $pagosIds = $request->prestamos_id ?? [];

            if (!empty($pagosIds)) {
                foreach ($pagosIds as $pagoId) {

                    // Obtener el registro de pago exacto
                    $pago = PagosPrestamos::where('id', $pagoId)
                        ->where('pagado', 0)
                        ->firstOrFail();

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
                                'wci'                => auth()->user()->id,
                            ]);

                            // Actualizar préstamo del aval
                            $rowPrestamo->increment('abona', $abonoReal);
                            $rowPrestamo->decrement('debe', $abonoReal);
                            $rowPrestamo->update([
                                'fecha_pago_reestructuracion' => now(),
                                'monto_pago_reestructuracion' => $pagoCapital,
                            ]);

                            // Actualizar socio aval
                            $aval = Socios::find($row->socios_id);
                            $lastId = Movimiento::max('id') ?? 0;
                            Movimiento::create([
                                'socios_id'       => $row->socios_id,
                                'fecha'           => now(),
                                'folio'           => 'MOV-' . ($lastId + 1),
                                'saldo_anterior'  => $aval->saldo,
                                'saldo_actual'    => $aval->saldo,
                                'monto'           => $abonoReal,
                                'movimiento'      => 'PAGO PRÉSTAMO',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago'     => 'EFECTIVO',
                                'estatus'         => 'EFECTUADO',
                            ]);
                            $aval->decrement('monto_prestamos', $abonoReal);

                            // Actualizar detalle aval
                            $row->increment('abona', $abonoReal);
                            $row->decrement('debe', $abonoReal);
                            $row->update([
                                'fecha_pago_reestructuracion' => now(),
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
                                'fecha_pago_reestructuracion' => now(),
                                'monto_pago_reestructuracion' => $pagoCapital,
                            ]);



                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id'       => $prestamo->id,
                                'socios_id'          => $prestamo->socios_id,
                                'tipo_cliente'       => 'SOCIO',
                                'abona'              => $capitalRestante + $interes,
                                'wci'                => auth()->user()->id,
                            ]);

                            // Movimiento del socio
                            $socio = Socios::find($prestamo->socios_id);
                            $lastId = Movimiento::max('id') ?? 0;
                            Movimiento::create([
                                'socios_id'       => $prestamo->socios_id,
                                'fecha'           => now(),
                                'folio'           => 'MOV-' . ($lastId + 1),
                                'saldo_anterior'  => $socio->saldo,
                                'saldo_actual'    => $socio->saldo - ($capitalRestante + $interes),
                                'monto'           => $capitalRestante + $interes,
                                'movimiento'      => 'PAGO PRÉSTAMO',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago'     => 'EFECTIVO',
                                'estatus'         => 'EFECTUADO',
                            ]);

                            $socio->decrement('monto_prestamos', $capitalRestante + $interes);
                            $socio->decrement('saldo', $capitalRestante + $interes);
                        }
                    } else {
                        // ----------- Sin avales -----------
                        $abonoReal = min($pagoCapital, $prestamo->debe);

                        $prestamo->increment('abona', $abonoReal);
                        $prestamo->decrement('debe', $abonoReal);
                        $prestamo->update([
                            'fecha_pago_reestructuracion' => now(),
                            'monto_pago_reestructuracion' => $pagoCapital,
                        ]);



                        PagosPrestamosDetalles::create([
                            'pagos_prestamos_id' => $idprestamoPago,
                            'prestamos_id'       => $prestamo->id,
                            'socios_id'          => $prestamo->socios_id,
                            'tipo_cliente'       => 'SOCIO',
                            'abona'              => $abonoReal,
                            'wci'                => auth()->user()->id,
                        ]);

                        $socio = Socios::find($prestamo->socios_id);
                        $lastId = Movimiento::max('id') ?? 0;
                        Movimiento::create([
                            'socios_id'       => $prestamo->socios_id,
                            'fecha'           => now(),
                            'folio'           => 'MOV-' . ($lastId + 1),
                            'saldo_anterior'  => $socio->saldo,
                            'saldo_actual'    => $socio->saldo - $abonoReal,
                            'monto'           => $abonoReal,
                            'movimiento'      => 'PAGO PRÉSTAMO',
                            'tipo_movimiento' => 'ABONO',
                            'metodo_pago'     => 'EFECTIVO',
                            'estatus'         => 'EFECTUADO',
                        ]);
                        $socio->decrement('monto_prestamos', $abonoReal);
                        $socio->decrement('saldo', $abonoReal);
                    }
                }

                // Eliminar IDs duplicados
                $prestamosIds = array_unique($prestamosIds);

                // Verificar fin de préstamo **una sola vez por préstamo**
                foreach ($prestamosIds as $id) {
                    $this->verificarFinPrestamo($id);
                }

                // Actualizar serie de los préstamos
                foreach ($prestamosIds as $id) {
                    $prestamoSerie = Prestamos::findOrFail($id);
                    $prestamoSerie->update([
                        'serie' => $prestamoSerie->total_quincenas
                    ]);
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

            /*
            if (isset($datosFormulario['prestamos_id']) && count($datosFormulario['prestamos_id']) > 0) {
                foreach ($request->prestamos_id as $key => $value) {

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
                            $rowPrestamo->fecha_pago_reestructuracion = Carbon::now();
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
                                'fecha_pago_reestructuracion' => Carbon::now(),
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
                                    'monto' => $capitalRestante + $interes,
                                    'movimiento' => 'PAGO PRÉSTAMO',
                                    'tipo_movimiento' => 'ABONO',
                                    'metodo_pago' => 'EFECTIVO',
                                    'estatus' => 'EFECTUADO',
                                ]);

                                //$socio->monto_prestamos = $socio->monto_prestamos - $capitalRestante;
                                $socio->monto_prestamos = $socio->monto_prestamos - ($capitalRestante + $interes );
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
                        $rowPrestamo->fecha_pago_reestructuracion = Carbon::now() ;
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
            */
            \DB::commit();
            //dd($prestamosIds);
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

    private function verificarFinPrestamo($prestamoId)
    {
        $ultimoPago = PagosPrestamos::where('prestamos_id', $prestamoId)
            ->orderByDesc('fecha_pago')
            ->first();

        if ($ultimoPago && $ultimoPago->pagado == 1) {
            $prestamo = Prestamos::find($prestamoId);

            if ($prestamo) {
                if ($prestamo->is_aval == 1) {
                    Socios::where('id', $prestamo->socios_id)
                        ->decrement('is_aval');
                } else {
                    Socios::where('id', $prestamo->socios_id)
                        ->decrement('numero_prestamos');
                }
            }
        }
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
                            $rowPrestamo->fecha_pago_reestructuracion = Carbon::now();
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
                                'fecha_pago_reestructuracion' => Carbon::now(),
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
                        $rowPrestamo->fecha_pago_reestructuracion = Carbon::now() ;
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

        $tipoValues = ['PAGO EFECTIVO DE DEUDAS', 'TRASLADO DE AHORRO'];

        return view('pagar_prestamos.show', compact('prestamo', 'socio','tipoValues'));

    }

    public function edit(Prestamos $prestamos)
    {
        //
    }

    public function update(Request $request, Prestamos $prestamos)
    {
        //
    }

    public function destroy(Prestamos $prestamos)
    {
        //
    }

    public function reciboLiquidaPrestamo($id, Request $request)
    {
        //$prestamosIds = $request->prestamos_id ?? [];

        $prestamosIds = json_decode($request->prestamos_ids, true) ?? [];

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
