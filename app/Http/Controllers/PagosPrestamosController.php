<?php

namespace App\Http\Controllers;

use App\Models\PagosPrestamos;
use App\Models\PrestamoDetalle;
use App\Models\PagosPrestamosDetalles;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Prestamos;
use App\Models\Socios;
use Carbon\Carbon;
use Exception;

class PagosPrestamosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:cargar-pago-prestamo-excel', ['only'=>['create', 'store','edit', 'update','destroy']]);
        $this->middleware('permission:historial-pago-prestamos', ['only'=>['index']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        return view('pagos_prestamos_excel.index');
    }

    public function create()
    {
        return view('pagos_prestamos_excel.create');
    }

    private function recalcularSeriePrestamo(int $prestamoId): void
    {
        $ultimaSeriePagada = PagosPrestamos::where('prestamos_id', $prestamoId)
            ->where('pagado', 1)
            ->max('serie_pago');

        Prestamos::where('id', $prestamoId)->update([
            'serie' => $ultimaSeriePagada ?? 0
        ]);
    }

    private function prestamoLiquidadoPorAdelanto(int $prestamoId): bool
    {
        $ultimoPago = PagosPrestamos::where('prestamos_id', $prestamoId)
            ->orderByDesc('serie_pago')
            ->first();

        return $ultimoPago && !is_null($ultimoPago->forma_pago);
    }


    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            // OBTENEMOS LOS DATOS PARA ABONAR EL PRESTAMO
            $datosFormulario = $request->all();

            if (isset($datosFormulario['prestamos_id']) && count($datosFormulario['prestamos_id']) > 0) {

                foreach ($request->prestamos_id as $key => $value) {

                    //BUSCAMOS EL REGISTRO PARA REALIZAR EL ABONO
                    $seriePendiente = PagosPrestamos::where('prestamos_id', $request->prestamos_id[$key])
                        ->where('pagado', 0)
                        ->min('serie_pago');

                    if (!$seriePendiente) {
                        continue; // ya no hay pagos pendientes
                    }

                    $prestamoPago = PagosPrestamos::where('prestamos_id', $request->prestamos_id[$key])
                        ->where('serie_pago', $seriePendiente)
                        ->where('pagado', 0)
                        ->first();

                    $idprestamoPago = 0;
                    if ($prestamoPago) {

                        $idprestamoPago = $prestamoPago->id;

                        $prestamoPago->update([
                            'pagado' => 1,
                            'fecha_pago' => $request->fecha_pago[$key],
                            'fecha_captura' => Carbon::now(),
                            'wci' => auth()->user()->id,
                        ]);
                    }

                    // BUSCAMOS SI EL PRESTAMO TIENE AVALES
                    $avales = PrestamoDetalle::where('prestamos_id', '=', $request->prestamos_id[$key])
                        ->where('debe', '>', 0)
                        ->get(['prestamo_detalles.*']);

                    if ($avales->count() > 0) {
                        $totalAvales = $avales->count();
                        $abonoAval = $prestamoPago->capital / $totalAvales;
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
                                /*if ($aval_socio) {
                                    $aval_socio->update([
                                        'is_aval' => $aval_socio->is_aval - 1,
                                    ]);
                                }*/
                                if ($avalDetalle->debe == 0 && !$this->prestamoLiquidadoPorAdelanto($avalDetalle->prestamos_id)) {
                                    $aval_socio->decrement('is_aval');
                                }

                            }
                        }

                        // ABONO DEL CLIENTE CON AVAL PERO CON EL RESTO DEL ABONO
                        // Calcula el capital restante después de los abonos a los avales
                        $capitalRestante = $prestamoPago->capital - $sumaAbonosAval;
                        if ($capitalRestante > 0) {
                            $rowPrestamo = Prestamos::findorfail($request->prestamos_id[$key]);
                            $interes = 0;//$rowPrestamo->total_intereses / $rowPrestamo->total_quincenas;

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
                                    'socios_id' => $request->socios_id[$key],
                                    'tipo_cliente' => 'SOCIO',
                                    'abona' => $capitalRestante,
                                    'wci' => auth()->user()->id,
                                ]);

                                // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                                $socio = Socios::find($request->socios_id[$key]);
                                // INSERTAMOS EL MOVIMIENTO
                                $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                                $nextId = $lastInsertedId + 1;
                                Movimiento::create([
                                    'socios_id' => $request->socios_id[$key],
                                    'fecha' => Carbon::now(),
                                    'folio' => 'MOV-' . $nextId,
                                    'saldo_anterior' => $socio->saldo,
                                    'saldo_actual' => $socio->saldo,
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
                        $abonoAvalCapital = 0;
                        $abonoAvalDescuento = 0;
                        $abonoAvalCapital = $prestamoPago->capital;
                        $abonoAvalDescuento = $prestamoPago->decuento;

                        // ABONO DEL CLIENTE SIN AVAL
                        //$abonoAval = $prestamoPago->capital;
                        $rowPrestamo = Prestamos::findorfail($request->prestamos_id[$key]);
                        $interes = 0; //$rowPrestamo->total_intereses / $rowPrestamo->total_quincenas;

                        // Restante disponible para abonar al aval
                        $restanteAval = $rowPrestamo->debe;

                        // Calcula el abono real al aval
                        $abonoReal = min($abonoAvalCapital, $restanteAval);
                        $abonoRealDescuento = min($abonoAvalDescuento, $restanteAval);

                        // ACTUALIZAMOS LOS VALORES DEL PRESTAMO
                        //$rowPrestamo->abona = $rowPrestamo->abona + $abonoReal; // Suma el abono al 'abona' existente
                        //$rowPrestamo->debe = $rowPrestamo->debe - $abonoReal; // Resta el abono de 'debe'
                        $rowPrestamo->abona = $rowPrestamo->abona + $abonoRealDescuento; // Suma el abono al 'abona' existente
                        $rowPrestamo->debe = $rowPrestamo->debe - $abonoRealDescuento; // Resta el abono de 'debe'
                        $rowPrestamo->save();

                        // ACTUALIZAMOS AL SOSCIO SI TIENE PRESTAMO
                        $socioDetalle = Prestamos::find($rowPrestamo->id);
                        if ($socioDetalle->debe == 0) {
                            $socio_socio = Socios::find($socioDetalle->socios_id);
                            /*if ($socio_socio) {
                                $socio_socio->update([
                                    'numero_prestamos' => $socio_socio->numero_prestamos - 1,
                                ]);
                            }*/
                            if ($socio_socio->debe == 0 && !$this->prestamoLiquidadoPorAdelanto($rowPrestamo->id)) {
                                $socio_socio->decrement('numero_prestamos');
                            }
                        }

                        //$abonoReal = $abonoReal - $interes;
                        $abonoRealDescuento = $abonoRealDescuento - $interes;
                        // CREAMOS EL DETALLE DEL PRESTAMO-ABONO
                        //if ($abonoReal > 0) {
                        if ($abonoRealDescuento > 0) {
                            PagosPrestamosDetalles::create([
                                'pagos_prestamos_id' => $idprestamoPago,
                                'prestamos_id' => $request->prestamos_id[$key],
                                'socios_id' => $rowPrestamo->socios_id,
                                'tipo_cliente' => 'SOCIO',
                                'abona' => $abonoRealDescuento,//$abonoReal,
                                'wci' => auth()->user()->id,
                            ]);

                            // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                            $socio = Socios::find($rowPrestamo->socios_id);
                            // INSERTAMOS EL MOVIMIENTO
                            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                            $nextId = $lastInsertedId + 1;
                            Movimiento::create([
                                'socios_id' => $request->socios_id[$key],
                                'fecha' => Carbon::now(),
                                'folio' => 'MOV-' . $nextId,
                                'saldo_anterior' => $socio->saldo,
                                'saldo_actual' => $socio->saldo,// - $abonoRealDescuento,
                                'monto' => $abonoRealDescuento,//$abonoReal,
                                'movimiento' => 'PAGO PRÉSTAMO',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago' => 'EFECTIVO',
                                'estatus' => 'EFECTUADO',
                            ]);
                            $socio->monto_prestamos = $socio->monto_prestamos - $abonoReal;
                            $socio->save();
                        }
                    }
                }

                // ACTUALIZAMOS LA SERIE DEL PRESTAMO
                foreach ($request->prestamos_id as $prestamoId) {
                    $this->recalcularSeriePrestamo($prestamoId);
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
            return redirect()->route('admin.socios.index')->with(['correcto' => 'success']);
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

    public function store2(Request $request)
    {
        try {
            \DB::beginTransaction();
            // OBTENEMOS LOS DATOS PARA ABONAR EL PRESTAMO
            $datosFormulario = $request->all();
            if (isset($datosFormulario['prestamos_id']) && count($datosFormulario['prestamos_id']) > 0) {
                foreach ($request->prestamos_id as $key => $value) {
                    $prestamoPago = new PagosPrestamos();
                    $prestamoPago->prestamos_id = $request->prestamos_id[$key];
                    $prestamoPago->socios_id = $request->socios_id[$key];
                    $prestamoPago->fecha_pago = $request->fecha_pago[$key];
                    $prestamoPago->fecha_captura = Carbon::now();
                    $prestamoPago->serie_pago = $request->serie_pago[$key];
                    $prestamoPago->serie_final = $request->serie_final[$key];
                    $prestamoPago->importe = $request->importe[$key];
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
                        $abonoAval = $request->importe[$key] / $totalAvales;
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
                        $capitalRestante = $request->importe[$key] - $sumaAbonosAval;
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
                                    'socios_id' => $request->socios_id[$key],
                                    'tipo_cliente' => 'SOCIO',
                                    'abona' => $capitalRestante,
                                    'wci' => auth()->user()->id,
                                ]);

                                // MODIFICAMOS LA TABLA SOCIOS PARA EL MONTO_PRESTAMO
                                $socio = Socios::find($request->socios_id[$key]);
                                // INSERTAMOS EL MOVIMIENTO
                                $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                                $nextId = $lastInsertedId + 1;
                                Movimiento::create([
                                    'socios_id' => $request->socios_id[$key],
                                    'fecha' => Carbon::now(),
                                    'folio' => 'MOV-' . $nextId,
                                    'saldo_anterior' => $socio->saldo,
                                    'saldo_actual' => $socio->saldo,
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
                        $abonoAval = $request->importe[$key];
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

                        $abonoReal = $abonoReal - $interes;
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
                                'socios_id' => $request->socios_id[$key],
                                'fecha' => Carbon::now(),
                                'folio' => 'MOV-' . $nextId,
                                'saldo_anterior' => $socio->saldo,
                                'saldo_actual' => $socio->saldo,
                                'monto' => $abonoReal,
                                'movimiento' => 'PAGO PRÉSTAMO',
                                'tipo_movimiento' => 'ABONO',
                                'metodo_pago' => 'EFECTIVO',
                                'estatus' => 'EFECTUADO',
                            ]);
                            $socio->monto_prestamos = $socio->monto_prestamos - $abonoReal;
                            $socio->save();
                        }
                    }
                }

                // ACTUALIZAMOS LA SERIE DEL PRESTAMO
                foreach ($request->prestamos_id as $key => $value) {
                    $prestamoSerie = Prestamos::findorfail($request->prestamos_id[$key]);
                    $prestamoSerie->update([
                        'serie' => $prestamoSerie->serie +1
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
            return redirect()->route('admin.socios.index')->with(['correcto' => 'success']);
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

    public function show(PagosPrestamos $pagosPrestamos)
    {

    }

    public function edit(PagosPrestamos $pagosPrestamos)
    {
        //
    }

    public function update(Request $request, PagosPrestamos $pagosPrestamos)
    {
        //
    }

    public function destroy(PagosPrestamos $pagosPrestamos)
    {
        //
    }

    public function leerArchivoExcelPago(Request $request)
    {
        if ($request->hasFile('archivo')) {

            // RESETEAMOS EL CAMPOR DE COMPARACION compara_pago
            Prestamos::where('estatus', '=', 'AUTORIZADO')
                ->where('prestamos.debe', '>', 0)
                ->where('prestamos.compara_pago', 1)
                ->update(['compara_pago' => 0]);

            $normalizarNombre = function ($nombre) {
                $nombre = preg_replace('/\s+/', '', $nombre); // eliminar espacios
                $nombre = strtr(
                    $nombre,
                    [
                        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U',
                        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
                        'Ñ' => 'N', 'ñ' => 'n'
                    ]
                );
                $nombre = mb_strtolower($nombre); // convertir a minúsculas
                return $nombre;
            };

            $archivo = $request->file('archivo');
            $fecha = $request->input('fecha_pago');
            $hoy = Carbon::now();
            $nuevaFecha = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
            $contador = 1;

            $allData = [];
            $allSerieRepetida = [];
            $allSerieOk = [];
            $allSerieNoDb = [];
            $allSerieNoExcel = [];

            // Obtén todos los rfc e importes de los registros de Excel
            $rfcList = [];
            $importeList = [];
            foreach ($worksheet->getRowIterator() as $row) {
                if ($contador === 1) {
                    $contador++; // Incrementa el contador
                    continue; // Salta este ciclo y pasa al siguiente registro
                }
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData['rfc'] = $worksheet->getCell('C' . $contador)->getValue();
                    $rowData['nombre_completo'] = $worksheet->getCell('D' . $contador)->getValue();
                    $rowData['serie_pago'] = $worksheet->getCell('E' . $contador)->getValue();
                    $rowData['serie_final'] = $worksheet->getCell('F' . $contador)->getValue();
                    $rowData['importe'] = $worksheet->getCell('H' . $contador)->getValue();
                }
                $rfcList[] = $rowData['rfc'];
                $importeList[] = $rowData['importe'];
                $allData[] = $rowData;
                $contador++;
            }

            // ONTENGO TODOS LOS PRÉSTAMOS, PARA OBTENER LOS PRESTAMOS QUE NO ESTÁN EN EL EXCEL
            $allPrestamos = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
                ->where('estatus', '=', 'AUTORIZADO')
                ->where('prestamos.debe', '>', 0)
                ->get(['prestamos.*', 'socios.id as socios_id', 'socios.nombre_completo', 'socios.rfc']);
            foreach ($allData as $data) {
                $nombreCompleto = $data['nombre_completo'];
                $importeE = $data['importe'];
                $serie = $data['serie_pago'];
                $encontrado = false;
                $importe  = number_format($importeE / 100, 2);
                // Buscar coincidencias en la base de datos
                foreach ($allPrestamos as $prestamo) {
                    $pago_quincenal = $prestamo->pago_quincenal;
                    $pagoQuincenal  = number_format($pago_quincenal / 100, 2);
                    if ($normalizarNombre($prestamo->nombre_completo) === $normalizarNombre($nombreCompleto) &&
                        $pagoQuincenal == $importe &&
                        $prestamo->compara_pago == 0 &&
                        ($prestamo->serie + 1) == $serie ) {
                        // Coincidencia encontrada, actualiza el campo COMPARA_PAGO a 1
                        $prestamo->update([
                            'compara_pago' => 1
                        ]);
                        $encontrado = true;
                        break; // Termina el bucle una vez que se ha encontrado una coincidencia
                    }
                }
                // Registra los datos repetidos
                if ($encontrado) {
                    //$allSerieRepetida[] = $data;
                } else {
                    // Registra los datos que no se encontraron en la base de datos
                    $serieNoDB = [
                        'rfc' => $data['rfc'],
                        'nombre_completo' => $data['nombre_completo'],
                        'serie' => $data['serie_pago'],
                        'importe' => $data['importe'],
                    ];
                    //$allSerieNoDb[] = $data;
                    $allSerieNoDb[] = $serieNoDB;
                }
            }

            // Encuentra los registros de la base de datos que no están en el archivo Excel
            foreach ($allPrestamos as $prestamo) {
                $encontrado = false;
                foreach ($allData as $data) {
                    if ($normalizarNombre($prestamo->nombre_completo) === $normalizarNombre($data['nombre_completo']) &&
                        number_format($prestamo->pago_quincenal / 100, 2) == number_format($data['importe'] / 100, 2)) {
                        $encontrado = true;
                        break;
                    }
                }
                if (!$encontrado) {
                    //$allSerieOk[] = $prestamo;
                    $serieNoExcel = [
                        'rfc' => $prestamo->rfc,
                        'nombre_completo' => $prestamo->nombre_completo,
                        'serie' => $prestamo->serie + 1,
                        'importe' => $prestamo->pago_quincenal,
                    ];
                    //$allSerieNoExcel[] = $prestamo;
                    $allSerieNoExcel[] = $serieNoExcel;
                }
            }

            // PAGO DE PRESTAMOS CORRECTOS
            $prestamosOk = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
                ->where('estatus', '=', 'AUTORIZADO')
                ->where('prestamos.debe', '>', 0)
                ->where('prestamos.compara_pago', 1)
                ->get(['prestamos.*', 'socios.id as socios_id', 'socios.nombre_completo', 'socios.rfc']);
            $totalImporte = 0; // Inicializa la variable
            foreach ($prestamosOk as $row) {
                $serieOk = [
                    'prestamos_id' => $row->id,
                    'socios_id' => $row->socios_id,
                    'fecha_pago' => $nuevaFecha,
                    'fecha_captura' => $hoy,
                    'serie_pago' => $row->serie +1 ,
                    'serie_final' => $row->total_quincenas,
                    'rfc' => $row->rfc,
                    'nombre_completo' => $row->nombre_completo,
                    'serie' => $row->serie +1,
                    'importe' => $row->pago_quincenal,
                ];
                $allSerieOk[] = $serieOk;
                // Suma al total
                $totalImporte += $row->pago_quincenal;
            }

            return response()->json(
                [
                    'result' => 'success',
                    'serie-ok' => $allSerieOk,
                    'serie-no-db' => $allSerieNoDb,
                    'serie-no-excel' => $allSerieNoExcel,
                    'importe_total' => $totalImporte
                ]
            );
        }
        return response()->json(['error' => 'No se ha proporcionado ningún archivo.']);
    }

    //INTENTO UNO
    /* public function leerArchivoExcelPago(Request $request)
    {
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $fecha = $request->input('fecha_pago');
            $hoy = Carbon::now();
            $nuevaFecha = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
            $contador = 1;

            $allData = [];
            $allSerieRepetida = [];
            $allSerieOk = [];
            $allSerieNoDb = [];

            // Obtén todos los rfc e importes de los registros de Excel
            $rfcList = [];
            $importeList = [];
            foreach ($worksheet->getRowIterator() as $row) {
                if ($contador === 1) {
                    $contador++; // Incrementa el contador
                    continue; // Salta este ciclo y pasa al siguiente registro
                }

                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData['rfc'] = $worksheet->getCell('C' . $contador)->getValue();
                    $rowData['nombre_completo'] = $worksheet->getCell('D' . $contador)->getValue();
                    $rowData['serie_pago'] = $worksheet->getCell('E' . $contador)->getValue();
                    $rowData['serie_final'] = $worksheet->getCell('F' . $contador)->getValue();
                    $rowData['importe'] = $worksheet->getCell('H' . $contador)->getValue();
                }

                $rfcList[] = $rowData['rfc'];
                $importeList[] = $rowData['importe'];
                $allData[] = $rowData;
                $contador++;
            }

            // ONTENGO TODOS LOS PRÉSTAMOS, PARA OBTENER LOS PRESTAMOS QUE NO ESTÁN EN EL EXCEL
            $allPrestamos = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
                ->where('estatus', '=', 'AUTORIZADO')
                ->where('prestamos.debe' ,'>', 0)
                ->get(['prestamos.*', 'socios.id as socios_id', 'socios.nombre_completo', 'socios.rfc']);
            // Filtrar los préstamos que coinciden con los datos del Excel
            $prestamosCoincidentes = $allPrestamos->filter(function ($prestamo) use ($rfcList, $importeList) {
                return in_array($prestamo->rfc, $rfcList) && in_array($prestamo->pago_quincenal, $importeList);
            });
            // Para obtener los préstamos que no están en el archivo Excel, puedes hacer una diferencia
            $prestamosNoEnExcel = $allPrestamos->diff($prestamosCoincidentes);

            // Consulta la base de datos una vez para obtener todos los préstamos relacionados
            $prestamos = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
                ->join('pagos_prestamos', 'prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                ->whereIn('socios.rfc', $rfcList)
                ->whereIn('pago_quincenal', $importeList)
                ->where('estatus', '=', 'AUTORIZADO')
                ->where('prestamos.debe' ,'>', 0)
                ->get(['prestamos.*', 'socios.id as socios_id', 'socios.nombre_completo', 'socios.rfc', 'pagos_prestamos.serie_pago']);

            foreach ($allData as $rowData) {
                $serieRepetida = null;
                $serieOk = null;
                $serieNoDB = null;

                // Encuentra los préstamos relacionados
                $relatedPrestamos = $prestamos->filter(function ($prestamo) use ($rowData) {
                    return $prestamo->rfc == $rowData['rfc']
                        && $prestamo->serie_pago == $rowData['serie_pago']
                        && $prestamo->pago_quincenal == $rowData['importe'];
                });

                if ($relatedPrestamos->count() > 0) {
                    // Tenemos una serie repetida
                    $serieRepetida = [
                        'rfc' => $rowData['rfc'],
                        'nombre_completo' => $rowData['nombre_completo'],
                        'serie' => $rowData['serie_pago'],
                        'importe' => $rowData['importe'],
                    ];
                } else {
                    // No hay series repetidas
                    // Consulta la base de datos para obtener los préstamos relacionados
                    $prestamosNoRepetidos = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
                        ->where('socios.rfc', $rowData['rfc'])
                        ->where('pago_quincenal', $rowData['importe'])
                        ->where('estatus', 'AUTORIZADO')
                        ->get(['prestamos.*', 'socios.id as socios_id', 'socios.nombre_completo', 'socios.rfc'])
                        ->first();

                    if ($prestamosNoRepetidos) {
                        $serieOk = [
                            'prestamos_id' => $prestamosNoRepetidos->id,
                            'socios_id' => $prestamosNoRepetidos->socios_id,
                            'fecha_pago' => $nuevaFecha,
                            'fecha_captura' => $hoy,
                            'serie_pago' => $rowData['serie_pago'],
                            'serie_final' => $prestamosNoRepetidos->total_quincenas,
                            'rfc' => $rowData['rfc'],
                            'nombre_completo' => $rowData['nombre_completo'],
                            'serie' => $rowData['serie_pago'],
                            'importe' => $rowData['importe'],
                        ];
                    } else {
                        $serieNoDB = [
                            'rfc' => $rowData['rfc'],
                            'nombre_completo' => $rowData['nombre_completo'],
                            'serie' => $rowData['serie_pago'],
                            'importe' => $rowData['importe'],
                        ];
                    }
                }

                $allSerieRepetida[] = $serieRepetida;
                $allSerieOk[] = $serieOk;
                $allSerieNoDb[] = $serieNoDB;
            }

            return response()->json(['result' => 'success', 'serie-ok' => $allSerieOk, 'serie-repetida' => $allSerieRepetida, 'serie-no-db' => $allSerieNoDb,'serie-no-excel' => $prestamosNoEnExcel]);
        }


        return response()->json(['error' => 'No se ha proporcionado ningún archivo.']);
    }*/
}
