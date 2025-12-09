<?php

namespace App\Http\Controllers;

use App\Models\Prestamos;
use App\Models\PrestamoDetalle;
use App\Models\Movimiento;
use App\Models\PagosPrestamos;
use App\Models\Socios;
use Carbon\Carbon;
use DB;
use PDF;

use Illuminate\Http\Request;

class TesoreriaPrestamoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:fianlizar-prestamo', ['only'=>['index','create', 'store','edit', 'update','destroy']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        $prestamos = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
            ->where('estatus', '=', 'PRE-AUTORIZADO')
            ->get(['prestamos.*', 'socios.nombre_completo']);
        
        return view('tesoreria_prestamo.index', compact('prestamos'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            $prestamo = Prestamos::findorfail($request->input('id'));
            $documentacionActual = $prestamo->documentacion ?? [];
            $socio = Socios::findorfail($prestamo->socios_id);
            $prestamoDetalle = PrestamoDetalle::where('prestamos_id', $prestamo->id)
                ->get();

            //$saldoAnteriro = $socio->saldo - (($socio->monto_prestamos - $prestamo->diferencia));
            //$saldoAnteriro = $socio->saldo - $prestamo->diferencia;
            $saldoAnteriro = $socio->saldo - ($socio->monto_prestamos);
            $saldoActual = $saldoAnteriro - $prestamo->diferencia;
            $fecha = $request->input('fecha_primer_pago');
            $nuevaFecha = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');

            $prestamo->fecha_prestamo = Carbon::now();
            $prestamo->fecha_primer_pago = $nuevaFecha;
            $prestamo->proximo_pago = $nuevaFecha;
            $prestamo->metodo_pago = $request->input('forma_pago');
            $prestamo->nota = strtoupper(nl2br($request->input('nota')));
            $prestamo->estatus = 'AUTORIZADO';
            $prestamo->documentacion = array_merge($documentacionActual, [
                'copia_talon' => $request->boolean('copia_talon') || ($documentacionActual['copia_talon'] ?? false),
                'copia_ine' => $request->boolean('copia_ine') || ($documentacionActual['copia_ine'] ?? false),
                'credencial_socio' => $request->boolean('credencial_socio') || ($documentacionActual['credencial_socio'] ?? false),
                'pagare' => $request->boolean('pagare') || ($documentacionActual['pagare'] ?? false),
                'solicitud' => $request->boolean('solicitud') || ($documentacionActual['solicitud'] ?? false),
            ]);
            $prestamo->save();

            // actualizamos los campos del socio
            $socio->update([
                'monto_prestamos' => $socio->monto_prestamos + $prestamo->diferencia,
                'numero_prestamos' => $socio->numero_prestamos + 1,
            ]);

            // OBTENEMOS LA TABLA DE INTERESES
            // INSERTAMOS TOTA LA TABLA DE LOS INTERESES EN pagos_prestamos
            $montoPrestamo = $prestamo->monto_prestamo;
            $mesesPrestamo = $prestamo->total_quincenas;
            $fechaPrimerPago = $prestamo->fecha_primer_pago;
            $response2 = $this->calcularTablaInteresDos($montoPrestamo, $mesesPrestamo, $fechaPrimerPago);
            $data = json_decode($response2->getContent(), true);
            $tablaInteresDos = $data['tabla_interesdos'];

            foreach ($tablaInteresDos as $fila) {
                $prestamoPago = new PagosPrestamos();
                $prestamoPago->prestamos_id = $prestamo->id;
                $prestamoPago->socios_id = $prestamo->socios_id;
                $prestamoPago->serie_pago = $fila['Pago'];
                $prestamoPago->serie_final = $prestamo->total_quincenas;
                $prestamoPago->importe = $prestamo->monto_prestamo;
                $prestamoPago->capital = floatval(str_replace(',', '', $fila['Capital']));
                $prestamoPago->interes = floatval(str_replace(',', '', $fila['Interes']));
                $prestamoPago->decuento = floatval(str_replace(',', '', $fila['Descuento']));

                if (\Carbon\Carbon::hasFormat($fila['Fecha_Pago'], 'd/m/Y')) {
                    $prestamoPago->fecha_tabla = \Carbon\Carbon::createFromFormat('d/m/Y', $fila['Fecha_Pago'])->format('Y-m-d');
                } else {
                    // puedes lanzar un error o poner null
                    $prestamoPago->fecha_tabla = null;
                }
                
                $prestamoPago->wci = auth()->user()->id;
                $prestamoPago->save();
            }

            // INSERTAMOS EN LA TABLA MOVIMIENTOS
            $nextId = Movimiento::max('id') + 1;
            Movimiento::create([
                'socios_id' => $prestamo->socios_id,
                'fecha' => Carbon::now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $saldoActual,
                'monto' => $prestamo->diferencia,
                'movimiento' => 'PRESTAMO AUTORIZADO',
                'tipo_movimiento' => 'CARGO',
                'metodo_pago' => $prestamo->metodo_pago,
                'estatus' => 'AUTORIZADO',
            ]);

            // MOVIMIENTOS PARA EL AVAL
            if (isset($prestamoDetalle)) {
                foreach ($prestamoDetalle as $row) {
                    $aval = Socios::findorfail($row->socios_id);
                    //$saldoAnteriroAval = ($aval->saldo + 500) - ($aval->monto_prestamos - $row->monto_aval);
                    //$saldoAnteriroAval = $aval->saldo - $row->monto_aval;
                    $saldoAnteriroAval = $aval->saldo - $aval->monto_prestamos;
                    //$saldoActualAval = $saldoAnteriroAval - ($row->monto_aval + $aval->monto_prestamos) ;
                    $saldoActualAval = $saldoAnteriroAval - $row->monto_aval ;

                    // actualizamos los campos del aval/socio
                    //if ( $request->input('apoyo_adicional') == '' ){
                    //    $aval->update([
                    //        'monto_prestamos' => $row->monto_prestamos + $row->monto_aval,
                    //        'is_aval' => $aval->is_aval + 1,
                    //    ]);
                    //}
                    //dd($row->monto_prestamos , $row->monto_aval);
                    $aval->update([
                        'monto_prestamos' => $aval->monto_prestamos + $row->monto_aval,
                        'is_aval' => $aval->is_aval + 1,
                    ]);

                    // INSERTAMOS EN LA TABLA MOVIMIENTOS, AVALES
                    $nextId = Movimiento::max('id') + 1;
                    Movimiento::create([
                        'socios_id' => $aval->id,
                        'fecha' => Carbon::now(),
                        'folio' => 'MOV-' . $nextId,
                        'saldo_anterior' => $saldoAnteriroAval,
                        'saldo_actual' => $saldoActualAval,
                        'monto' => $row->monto_aval,
                        'movimiento' => 'PRESTAMO AUTORIZADO AVAL',
                        'tipo_movimiento' => 'CARGO',
                        'metodo_pago' => $prestamo->metodo_pago,
                        'estatus' => 'AUTORIZADO',
                    ]);
                }
            }

            DB::commit();
            //return redirect()->route('admin.tesoreria.retiro.index')->with(['id' => $prestamo->id]);
            return json_encode($prestamo->id);
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show($id)
    {
        $prestamo = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
            ->select('prestamos.*', 'socios.nombre_completo')
            ->where('prestamos.id', $id)
            ->first(); // ← usa first() para objeto único
            //->get();

        return json_encode($prestamo);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Request $request,$id)
    {
        try {
            \DB::beginTransaction();

            $prestamo = Prestamos::findorfail($request->input('id'));
            $prestamoDetalle = PrestamoDetalle::where('prestamos_id', $prestamo->id)
                ->get();
            $socio = Socios::findorfail($prestamo->socios_id);
            $anterior = $socio->saldo - (($socio->monto_prestamos - $prestamo->diferencia));
            $saldoAnteriro = $anterior - $prestamo->diferencia;
            $saldoActual = $saldoAnteriro + $prestamo->diferencia;

            $prestamo->estatus = 'CANCELADO';
            $prestamo->motivo_cancelacion = nl2br($request->input('comentarios')).". ".Carbon::now();
            $prestamo->save();

            // AUMENTAMOS EL SALDO DEL PRESTAMO DEL SOCIO
            if ($prestamo->prestamo_especial == 0){
                $socio = Socios::findorfail($prestamo->socios_id);
                $montoDevuelto = $prestamo->diferencia;
                //$socio->update([
                //    'monto_prestamos' => $socio->monto_prestamos - $montoDevuelto,
                //    'numero_prestamos' => $socio->numero_prestamos - 1,
                //]);
            

                // INSERTAMOS EN LA TABLA MOVIMIENTOS
                $nextId = Movimiento::max('id') + 1;
                Movimiento::create([
                    'socios_id' => $prestamo->socios_id,
                    'fecha' => Carbon::now(),
                    'folio' => 'MOV-' . $nextId,
                    'monto' => $montoDevuelto,
                    'saldo_anterior' => $saldoAnteriro,
                    'saldo_actual' => $saldoActual,
                    'movimiento' => 'PRESTAMO CANCELADO',
                    'tipo_movimiento' => 'ABONO',
                    'metodo_pago' => 'EFECTIVO',
                    'estatus' => 'CANCELADO',
                ]);
            }else if($prestamo->prestamo_especial == 1){
                $socio = Socios::findorfail($prestamo->socios_id);
                // INSERTAMOS EN LA TABLA MOVIMIENTOS
                $nextId = Movimiento::max('id') + 1;
                Movimiento::create([
                    'socios_id' => $prestamo->socios_id,
                    'fecha' => Carbon::now(),
                    'folio' => 'MOV-' . $nextId,
                    'monto' => 0,
                    'saldo_anterior' => $socio->saldo - ($socio->monto_prestamos),
                    'saldo_actual' => $socio->saldo - ($socio->monto_prestamos),
                    'movimiento' => 'PRESTAMO CANCELADO',
                    'tipo_movimiento' => 'ABONO',
                    'metodo_pago' => 'EFECTIVO',
                    'estatus' => 'CANCELADO',
                ]);
            }

            // MOVIMIENTOS PARA EL AVAL
            if (isset($prestamoDetalle)) {
                foreach ($prestamoDetalle as $row) {
                    $aval = Socios::findorfail($row->socios_id);
                    $anteriorAval = $aval->saldo - (($aval->monto_prestamos - $row->monto_aval));
                    $saldoAnteriroAval = $anteriorAval - $row->monto_aval;
                    $saldoActualAval = $saldoAnteriroAval + $row->monto_aval;

                    //$aval->update([
                    //    'monto_prestamos' => $aval->monto_prestamos - $row->monto_aval,
                    //    'is_aval' => $aval->is_aval - 1,
                    //]);
                    // INSERTAMOS EN LA TABLA MOVIMIENTOS, AVALES
                    $nextId = Movimiento::max('id') + 1;
                    Movimiento::create([
                        'socios_id' => $aval->id,
                        'fecha' => Carbon::now(),
                        'folio' => 'MOV-' . $nextId,
                        'saldo_anterior' => $saldoAnteriroAval,
                        'saldo_actual' => $saldoActualAval,
                        'monto' => $row->monto_aval,
                        'movimiento' => 'PRESTAMO CANCELADO AVAL',
                        'tipo_movimiento' => 'ABONO',
                        'metodo_pago' => 'EFECTIVO',
                        'estatus' => 'CANCELADO',
                    ]);
                }
            }

            DB::commit();
            //return redirect()->route('admin.tesoreria.retiro.index')->with(['id' => $prestamo->id]);
            return json_encode($prestamo->id);
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            //return json_encode($query);
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function reciboPrestamo($id)
    {
        $prestamo = Prestamos::findorfail($id);
        $socio = Socios::findorfail($prestamo->socios_id);
        $prestamoDetalle = PrestamoDetalle::join('socios', 'socios.id', '=', 'prestamo_detalles.socios_id')
            ->where('prestamos_id', $prestamo->id)
            ->select('prestamo_detalles.*', 'socios.nombre_completo', 'socios.rfc', 'socios.saldo', 'socios.monto_prestamos')
            ->get();
        
        
        // OBTENEMOS LA TABLA DE INTERESES
        $montoPrestamo = $prestamo->monto_prestamo;
        $mesesPrestamo = $prestamo->total_quincenas;
        $fechaPrimerPago = $prestamo->fecha_primer_pago;
        $response2 = $this->calcularTablaInteresDos($montoPrestamo, $mesesPrestamo, $fechaPrimerPago);
        $jsonContent = $response2->getContent();
        $tblDos = json_decode($jsonContent, true);
        // FIN OBTENEMOS TABLA DE INTERESES

        //  - CREAMOS EL PDF ----
        if( $prestamo->prestamo_especial == 0){
            //dd($tblDos);
            $pdf = PDF::loadView('recibos.recibo_prestamos', compact('prestamo', 'socio', 'prestamoDetalle','tblDos'))
                ->setPaper('letter', 'portrait');

            return $pdf->stream();
        }else if ($prestamo->prestamo_especial == 1){
            $response2 = $this->calcularTablaInteresDosNoInteres($montoPrestamo, $mesesPrestamo, $fechaPrimerPago);
            $jsonContent = $response2->getContent();
            $tblDos = json_decode($jsonContent, true);
            $pdf = PDF::loadView('recibos.recibo_prestamos_especiales', compact('prestamo', 'socio', 'prestamoDetalle','tblDos'))
                ->setPaper('letter', 'portrait');

            return $pdf->stream();
        }
    }

    // (FUNCIONES) PRUBAS PARA GENERARA LA TABLA DE INTERESES
    public function round($d)
    {
        $dAbs = abs($d);
        $i = intval($dAbs);
        $result = $dAbs - $i;

        if ($result < 0.001) {
            return $d < 0 ? -$i : $i;
        } else {
            return $d < 0 ? - ($i + 1) : $i + 1;
        }
    }

    public function round1($d)
    {
        $dAbs = abs($d);
        $i = intval($dAbs);
        $result = $dAbs - $i;

        if ($result < 0.5) {
            return $d < 0 ? -$i : $i;
        } else {
            return $d < 0 ? - ($i + 1) : $i + 1;
        }
    }

    protected static $totalcapint = 0;
    protected static $totalinteres = 0;
    protected static $interesid = 0;

    public function calcularTablaInteresUno($montoPrestamo, $mesesPrestamo)
    {
        $saldo = $montoPrestamo;
        self::$totalcapint = 0;

        $pagos = [];
        self::$totalinteres = 0;
        $pagomonto = 0;
        $interes = 0;
        $pagomontored = 0;
        $interesred = 0;
        $capinter = 0;
        $saldofinal = 0;
        $pagomonto = $montoPrestamo / $mesesPrestamo;
        for ($i = 1; $i <= $mesesPrestamo; $i++) {
            $pagomontored = ($montoPrestamo >= $pagomonto) ? $this->round($pagomonto) : $this->round($montoPrestamo);

            $interes = ($montoPrestamo / 100) * 1.5; // TASA DE INTERES
            $interesred = $this->round1($interes);
            $capinter = $pagomontored + $interesred;
            self::$totalinteres +=  $interesred;
            self::$totalcapint = self::$totalcapint + $capinter;
            $saldofinal = intval($montoPrestamo) - $pagomontored;
            $montoPrestamo = $saldofinal;

            $pagos[] = [
                'Pago' => $i,
                'Capital' => $pagomontored,
                'Interes' => $interesred,
                'Descuento' => $pagomontored + $interesred,
                'Saldo_Final' => $saldofinal,
            ];
        }
        return response()->json([
            'tabla_interesuno' => $pagos,
            'total_interes' => self::$totalinteres,
            'total_capital_interes' => self::$totalinteres + $saldo,
        ]);
    }

    public function calcularTablaInteresDos($montoPrestamo, $mesesPrestamo, $fechaPrimerPago)
    {
        $montoprest = 0;
        $mesesprest = 0;
        $pagouno = 0;
        $pagouno1 = 0;
        $capital = 0;
        $saldofinald = 0;

        $montoprest = $montoPrestamo;
        $mesesprest = $mesesPrestamo;
        $primerIteracion = true;
        $valorColumna4 = 0; // $able para almacenar el valor de la columna 4
        $valorAnteriorSaldofinald = 0;
        $capitalAnterior = 0;

        // Llamar a calcularTablaInteresUno y obtener la respuesta JSON
        $response = $this->calcularTablaInteresUno($montoprest, $mesesprest);
        // Decodificar la respuesta JSON en un arreglo en PHP
        $data = json_decode($response->getContent(), true);
        // Acceder a los valores de 'tabla_interesuno' en el arreglo $data
        $tablaInteresUno = $data['tabla_interesuno'];
        $tblDos = [];
        $fechaActual = Carbon::parse($fechaPrimerPago);
        for ($i = 1; $i <= $mesesprest; $i++) {
            if ($i == 1) {
                $pagouno1 = self::$totalcapint / $mesesprest;
                $pagounost = number_format($pagouno1, 1); // pagouno1.toFixed(1);
                $pagounost2 = str_replace(',', '', $pagounost); // Reemplazar la coma por el punto
                $pagouno = floatval($pagounost2);

                //echo ($pagouno);
                //$primerElemento = $("#tabla-interesuno tbody tr").first();
                //$interesado = primerElemento.find("td").eq(2).text();
                $primerElemento = $tablaInteresUno[0];
                $interesado = $primerElemento['Interes'];

                $interesValor = floatval($interesado);
                self::$interesid += $interesValor;
                $capital = (self::$totalcapint / $mesesprest) - floatval($primerElemento['Interes']);
                $saldofinald = $montoprest - $capital;
                ///// redondeando la variable $saldofinald
                // Redondear a dos decimales y obtener el segundo decimal
                $saldofinald_redondeado = round($saldofinald, 2);
                if ($saldofinald_redondeado == 0.0) {
                    // El valor no tiene decimales, dejarlo igual
                    $saldofinald_final = floatval($valorAnteriorSaldofinald);
                } else {
                    $saldofinald_final = round($saldofinald_redondeado, 1, PHP_ROUND_HALF_DOWN);
                }
                //// FIN redondeando la variable $saldofinald
                // creamos la tabla
                $tblDos[] = [
                    'Pago' => $i,
                    'Capital' => number_format($capital, 1),
                    'Interes' => $interesado,
                    'Descuento' => number_format($pagouno, 1),
                    'Saldo_Final' => number_format($saldofinald_final, 1),
                    'Fecha_Pago' => $fechaActual->format('d/m/Y'),
                ];
                $capital = $pagouno - self::$interesid;
                $valorColumna4 = $saldofinald_final; //number_format($saldofinald, 1); //$saldofinald.toFixed(1);
            } else if ($primerIteracion) {
                self::$interesid = 0;
                $contador = 2; // Empieza desde 2

                $primerElemento = true;
                foreach ($tablaInteresUno as $elemento) {
                    // Verifica si es la primera iteración
                    if ($primerElemento) {
                        $primerElemento = false;
                        continue; // Saltar la primera iteración
                    }
                    $interesado = $elemento['Interes']; //$(this).find("td").eq(2).text();
                    $interesValor = floatval($interesado);
                    self::$interesid += $interesValor;
                    $capital = (self::$totalcapint / $mesesprest) - $elemento['Interes'];
                    if ($contador == 2) {
                        $numeroCol4 = $valorColumna4;
                        $numeroCol4 = str_replace(",", "", $numeroCol4); // Eliminar la coma
                        $numeroCol4 = floatval($numeroCol4);

                        $saldofinald = $numeroCol4 - $capital; // $saldofinald = $valorColumna4 - $capital;

                        ///// redondeando la variable $saldofinald
                        // Redondear a dos decimales
                        $saldofinald_redondeado = round($saldofinald, 2);

                        if ($saldofinald_redondeado == 0.0) {
                            // El valor no tiene decimales, dejarlo igual
                            $saldofinald_final = floatval($valorAnteriorSaldofinald);
                        } else {
                            $saldofinald_final = round($saldofinald_redondeado, 1, PHP_ROUND_HALF_DOWN);
                        }
                        //// FIN redondeando la variable $saldofinald
                        $valorAnteriorSaldofinald = $saldofinald_final; //$saldofinald;
                    } else {
                        //$saldofinald = $valorAnteriorSaldofinald.toFixed(1) - $capital.toFixed(1);
                        $saldofinald = floatval($valorAnteriorSaldofinald) - floatval($capital);

                        ///// redondeando la variable $saldofinald
                        // Redondear a dos decimales
                        $saldofinald_redondeado = round($saldofinald, 2);

                        if ($saldofinald_redondeado == 0.0) {
                            // El valor no tiene decimales, dejarlo igual
                            $saldofinald_final = floatval($valorAnteriorSaldofinald);
                        } else {
                            $saldofinald_final = round($saldofinald_redondeado, 1, PHP_ROUND_HALF_DOWN);
                        }
                        //// FIN redondeando la variable $saldofinald
                        if ($contador < $mesesprest) {
                            //$capital = (self::$totalcapint / $mesesprest) - $(this).find("td").eq(2).text();
                            $capital = (self::$totalcapint / $mesesprest) - $elemento['Interes'];
                        } else if ($contador == $mesesprest) {
                            $capital = $valorAnteriorSaldofinald;
                            $pagouno = floatval($capital) + floatval($interesado);
                            $saldofinald = 0;
                            $saldofinald_final = 0;
                        }
                    }
                    // creamos la tabla
                    if ($fechaActual->day == 15) {
                        // Si la fecha actual es el día 15, avanzamos a fin de mes
                        $fechaActual->endOfMonth();
                    } else {
                        // Si no, avanzamos 15 días
                        $fechaActual->addDays(15);
                    }
                    $tblDos[] = [
                        'Pago' => $contador,
                        'Capital' => number_format($capital, 1),
                        'Interes' => $interesado,
                        'Descuento' => number_format($pagouno, 1),
                        //'Saldo_Final' => number_format(floatval($saldofinald), 1),//number_format($saldofinald, 1),
                        'Saldo_Final' => number_format(floatval($saldofinald_final), 1), //number_format($saldofinald, 1),
                        'Fecha_Pago' => $fechaActual->format('d/m/Y'),
                    ];
                    $valorAnteriorSaldofinald = $saldofinald_final; //$saldofinald;
                    $capitalAnterior = number_format($capital, 1); //$capital.toFixed(1);
                    $contador++;
                }
                $primerIteracion = false;
            }
        }
        return response()->json([
            'tabla_interesdos' => $tblDos,
        ]);
    }

    public function calcularTablaInteresUnoNoInteres($montoPrestamo, $mesesPrestamo)
    {
        $saldo = $montoPrestamo;
        self::$totalcapint = 0;

        $pagos = [];
        self::$totalinteres = 0;
        $pagomonto = 0;
        $interes = 0;
        $pagomontored = 0;
        $interesred = 0;
        $capinter = 0;
        $saldofinal = 0;
        $pagomonto = $montoPrestamo / $mesesPrestamo;
        for ($i = 1; $i <= $mesesPrestamo; $i++) {
            $pagomontored = ($montoPrestamo >= $pagomonto) ? $this->round($pagomonto) : $this->round($montoPrestamo);

            $interes = 0; //($montoPrestamo / 100) * 1.5; // TASA DE INTERES
            $interesred = $this->round1($interes);
            $capinter = $pagomontored + $interesred;
            self::$totalinteres +=  $interesred;
            self::$totalcapint = self::$totalcapint + $capinter;
            $saldofinal = intval($montoPrestamo) - $pagomontored;
            $montoPrestamo = $saldofinal;

            $pagos[] = [
                'Pago' => $i,
                'Capital' => $pagomontored,
                'Interes' => $interesred,
                'Descuento' => $pagomontored + $interesred,
                'Saldo_Final' => $saldofinal,
            ];
        }
        return response()->json([
            'tabla_interesuno' => $pagos,
            'total_interes' => self::$totalinteres,
            'total_capital_interes' => self::$totalinteres + $saldo,
        ]);
    }

    public function calcularTablaInteresDosNoInteres($montoPrestamo, $mesesPrestamo, $fechaPrimerPago)
    {
        $montoprest = 0;
        $mesesprest = 0;
        $pagouno = 0;
        $pagouno1 = 0;
        $capital = 0;
        $saldofinald = 0;

        $montoprest = $montoPrestamo;
        $mesesprest = $mesesPrestamo;
        $primerIteracion = true;
        $valorColumna4 = 0; // $able para almacenar el valor de la columna 4
        $valorAnteriorSaldofinald = 0;
        $capitalAnterior = 0;

        // Llamar a calcularTablaInteresUno y obtener la respuesta JSON
        $response = $this->calcularTablaInteresUnoNoInteres($montoprest, $mesesprest);
        // Decodificar la respuesta JSON en un arreglo en PHP
        $data = json_decode($response->getContent(), true);
        // Acceder a los valores de 'tabla_interesuno' en el arreglo $data
        $tablaInteresUno = $data['tabla_interesuno'];
        $tblDos = [];
        $fechaActual = Carbon::parse($fechaPrimerPago);
        for ($i = 1; $i <= $mesesprest; $i++) {
            if ($i == 1) {
                $pagouno1 = self::$totalcapint / $mesesprest;
                $pagounost = number_format($pagouno1, 1); // pagouno1.toFixed(1);
                $pagounost2 = str_replace(',', '', $pagounost); // Reemplazar la coma por el punto
                $pagouno = floatval($pagounost2);

                //$primerElemento = $("#tabla-interesuno tbody tr").first();
                //$interesado = primerElemento.find("td").eq(2).text();
                $primerElemento = $tablaInteresUno[0];
                $interesado = $primerElemento['Interes'];

                $interesValor = floatval($interesado);
                self::$interesid += $interesValor;
                $capital = (self::$totalcapint / $mesesprest) - floatval($primerElemento['Interes']);
                $saldofinald = $montoprest - $capital;
                ///// redondeando la variable $saldofinald
                // Redondear a dos decimales y obtener el segundo decimal
                $saldofinald_redondeado = round($saldofinald, 2);
                if ($saldofinald_redondeado == 0.0) {
                    // El valor no tiene decimales, dejarlo igual
                    $saldofinald_final = floatval($valorAnteriorSaldofinald);
                } else {
                    $saldofinald_final = round($saldofinald_redondeado, 1, PHP_ROUND_HALF_DOWN);
                }
                //// FIN redondeando la variable $saldofinald
                // creamos la tabla
                $tblDos[] = [
                    'Pago' => $i,
                    'Capital' => number_format($capital, 1),
                    'Interes' => $interesado,
                    'Descuento' => number_format($pagouno, 1),
                    'Saldo_Final' => number_format($saldofinald_final, 1),
                    'Fecha_Pago' => $fechaActual->format('d/m/Y'),
                ];
                $capital = $pagouno - self::$interesid;
                $valorColumna4 = $saldofinald_final; //number_format($saldofinald, 1); //$saldofinald.toFixed(1);
            } else if ($primerIteracion) {
                self::$interesid = 0;
                $contador = 2; // Empieza desde 2

                $primerElemento = true;
                foreach ($tablaInteresUno as $elemento) {
                    // Verifica si es la primera iteración
                    if ($primerElemento) {
                        $primerElemento = false;
                        continue; // Saltar la primera iteración
                    }
                    $interesado = $elemento['Interes']; //$(this).find("td").eq(2).text();
                    $interesValor = floatval($interesado);
                    self::$interesid += $interesValor;
                    $capital = (self::$totalcapint / $mesesprest) - $elemento['Interes'];
                    if ($contador == 2) {
                        $numeroCol4 = $valorColumna4;
                        $numeroCol4 = str_replace(",", "", $numeroCol4); // Eliminar la coma
                        $numeroCol4 = floatval($numeroCol4);

                        $saldofinald = $numeroCol4 - $capital; // $saldofinald = $valorColumna4 - $capital;

                        ///// redondeando la variable $saldofinald
                        // Redondear a dos decimales
                        $saldofinald_redondeado = round($saldofinald, 2);

                        if ($saldofinald_redondeado == 0.0) {
                            // El valor no tiene decimales, dejarlo igual
                            $saldofinald_final = floatval($valorAnteriorSaldofinald);
                        } else {
                            $saldofinald_final = round($saldofinald_redondeado, 1, PHP_ROUND_HALF_DOWN);
                        }
                        //// FIN redondeando la variable $saldofinald
                        $valorAnteriorSaldofinald = $saldofinald_final; //$saldofinald;
                    } else {
                        //$saldofinald = $valorAnteriorSaldofinald.toFixed(1) - $capital.toFixed(1);
                        $saldofinald = floatval($valorAnteriorSaldofinald) - floatval($capital);

                        ///// redondeando la variable $saldofinald
                        // Redondear a dos decimales
                        $saldofinald_redondeado = round($saldofinald, 2);

                        if ($saldofinald_redondeado == 0.0) {
                            // El valor no tiene decimales, dejarlo igual
                            $saldofinald_final = floatval($valorAnteriorSaldofinald);
                        } else {
                            $saldofinald_final = round($saldofinald_redondeado, 1, PHP_ROUND_HALF_DOWN);
                        }
                        //// FIN redondeando la variable $saldofinald
                        if ($contador < $mesesprest) {
                            //$capital = (self::$totalcapint / $mesesprest) - $(this).find("td").eq(2).text();
                            $capital = (self::$totalcapint / $mesesprest) - $elemento['Interes'];
                        } else if ($contador == $mesesprest) {
                            $capital = $valorAnteriorSaldofinald;
                            $pagouno = floatval($capital) + floatval($interesado);
                            $saldofinald = 0;
                            $saldofinald_final = 0;
                        }
                    }
                    // creamos la tabla
                    if ($fechaActual->day == 15) {
                        // Si la fecha actual es el día 15, avanzamos a fin de mes
                        $fechaActual->endOfMonth();
                    } else {
                        // Si no, avanzamos 15 días
                        $fechaActual->addDays(15);
                    }
                    $tblDos[] = [
                        'Pago' => $contador,
                        'Capital' => number_format($capital, 1),
                        'Interes' => $interesado,
                        'Descuento' => number_format($pagouno, 1),
                        //'Saldo_Final' => number_format(floatval($saldofinald), 1),//number_format($saldofinald, 1),
                        'Saldo_Final' => number_format(floatval($saldofinald_final), 1), //number_format($saldofinald, 1),
                        'Fecha_Pago' => $fechaActual->format('d/m/Y'),
                    ];
                    $valorAnteriorSaldofinald = $saldofinald_final; //$saldofinald;
                    $capitalAnterior = number_format($capital, 1); //$capital.toFixed(1);
                    $contador++;
                }
                $primerIteracion = false;
            }
        }
        return response()->json([
            'tabla_interesdos' => $tblDos,
        ]);
    }
}
