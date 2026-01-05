<?php

namespace App\Http\Controllers;

use App\Models\Prestamos;
use App\Models\PrestamoDetalle;
use App\Models\Socios;
use App\Models\Movimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use PDF;
use DB;
use Illuminate\Support\Facades\Hash;

class PrestamosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:crear-prestamo', ['only'=>['index','create', 'store','show']]);
        $this->middleware('permission:prestamos-diarios', ['only'=>['index']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        $prestamos = Prestamos::join('socios', 'prestamos.socios_id', '=', 'socios.id')
            ->where('estatus', '=', 'AUTORIZADO')
            ->get(['prestamos.*', 'socios.nombre_completo']);

        return view('prestamos.index', compact('prestamos'));
    }

    public function create()
    {
        $prestamo = new Prestamos;
        $edit = false;
        $prestamo->setRelation('detalles', collect());

        return view('prestamos.create', compact('prestamo','edit'));
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            $socio = Socios::findorfail($request->input('socios_id'));
            $prestamo = new Prestamos();
            $fecha = $request->input('fecha_primer_pago');
            $nuevaFecha = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            $saldoSocio = $request->input('apoyo_adicional') == '' ?
                $request->input('saldo_socio') :
                $request->input('saldo_socio') - 5000;
            //dd($saldoSocio);

            $prestamo->socios_id = $request->input('socios_id');
            $prestamo->fecha_captura = Carbon::now();
            //$prestamo->fecha_prestamo = Carbon::now();
            $prestamo->monto_prestamo = $request->input('monto_prestamo');
            $prestamo->total_intereses = $request->input('prestamo_intereses') - $request->input('monto_prestamo');
            $prestamo->pago_quincenal = $request->input('pago_quincenal');
            $prestamo->total_quincenas = $request->input('total_quincenas');
            $prestamo->debia = $request->input('monto_prestamo') + ($request->input('prestamo_intereses') - $request->input('monto_prestamo'));
            $prestamo->abona = 0;
            $prestamo->debe = $request->input('monto_prestamo') + ($request->input('prestamo_intereses') - $request->input('monto_prestamo'));
            //$prestamo->serie = $request->input('serie');
            //$prestamo->saldo_capital = floatval(str_replace('$', '', $request->input('disponible_socio')));// $request->input('disponible_socio');
            //$prestamo->saldo_interes = $request->input('total_cap_interes');
            //$prestamo->saldo_total = floatval(str_replace('$', '', $request->input('saldo'))); //$request->input('saldo');
            //$prestamo->metodo_pago = $request->input('metodo_pago');
            $prestamo->diferencia = $saldoSocio;
            $prestamo->fecha_primer_pago = $nuevaFecha;
            $prestamo->proximo_pago = $nuevaFecha;
            $prestamo->folio = $request->input('folio');
            $prestamo->num_nomina = $request->input('num_nomina');
            $prestamo->num_empleado = $request->input('num_empleado');
            //$prestamo->fecha_primer_corte = $request->input('fecha_primer_corte');
            $prestamo->apoyo_adicional = $request->input('apoyo_adicional') == '' ?
                                        0 : $request->input('apoyo_adicional');
            $prestamo->estatus = 'PRE-AUTORIZADO';
            // Guardamos el JSON de documentación
            $prestamo->documentacion = [
                'copia_talon' => $request->has('copia_talon'),
                'copia_ine' => $request->has('copia_ine'),
                'credencial_socio' => $request->has('credencial_socio'),
                'pagare' => $request->has('pagare'),
                'solicitud' => $request->has('solicitud'),
            ];
            $prestamo->save();
            $id = $prestamo->id;
            $montoPrestamo = $prestamo->diferencia;

            // actualizamos los campos del socio
            //$socio->update([
            //    'monto_prestamos' => $socio->monto_prestamos + $request->input('monto_prestamo'),
            //    'numero_prestamos' => $socio->numero_prestamos + 1,
            //]);

            // INSERTAMOS EN LA TABLA MOVIMIENTOS
            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
            $nextId = $lastInsertedId + 1;
            $saldoAnteriro = ($socio->saldo - $montoPrestamo) + $montoPrestamo;
            $saldoActual = $saldoAnteriro - $saldoSocio;

            /*Movimiento::create([
                'socios_id' => $request->input('socios_id'),
                'fecha' => Carbon::now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $saldoActual,
                'monto' => $saldoSocio,
                'movimiento' => 'PRESTAMO PRE AUTORIZADO',
                'tipo_movimiento' => 'CARGO',
                'metodo_pago' => 'POR DEFINIR',
                'estatus' => 'PRE-AUTORIZADO',
            ]);*/

            $movSocio = $prestamo->movimientos()->create([
                'socios_id'       => $request->input('socios_id'),
                'fecha'           => Carbon::now(),
                'folio'           => 'MOV-',
                'saldo_anterior'  => $saldoAnteriro,
                'saldo_actual'    => $saldoActual,
                'monto'           => $saldoSocio,
                'movimiento'      => 'PRESTAMO PRE AUTORIZADO',
                'tipo_movimiento' => 'CARGO',
                'metodo_pago'     => 'POR DEFINIR',
                'estatus'         => 'PRE-AUTORIZADO',
            ]);

            $movSocio->update([
                'folio' => 'MOV-' . $movSocio->id,
            ]);


            // Obtener los datos del formulario
            $datosFormulario = $request->all();
            // dd($datosFormulario);
            // Verificar si idAval está presente y contiene elementos
            if (isset($datosFormulario['idAval']) && count($datosFormulario['idAval']) > 0) {
                // Recorrer los elementos de idAval
                //foreach ($datosFormulario['idAval'] as $id) {
                foreach ($request->idAval as $key => $value) {
                    $aval = Socios::findorfail($request->idAval[$key]);

                    $prestamoDetalle = new PrestamoDetalle();
                    $prestamoDetalle->prestamos_id = $prestamo->id;
                    $prestamoDetalle->socios_id = $request->idAval[$key];
                    $prestamoDetalle->aval = $aval->nombre_completo;
                    $prestamoDetalle->num_aval = $aval->num_socio;
                    $prestamoDetalle->monto_socio = $saldoSocio; //$request->input('saldo_socio');
                    $prestamoDetalle->monto_aval = $request->saldo_aval[$key]; //$datosFormulario['saldo_aval'][$id];
                    $prestamoDetalle->debia = $request->saldo_aval[$key];
                    $prestamoDetalle->abona = 0;
                    $prestamoDetalle->debe = $request->saldo_aval[$key];
                    $prestamoDetalle->num_nomina = $request->input('num_nomina');
                    $prestamoDetalle->num_empleado = $request->input('num_empleado');
                    $prestamoDetalle->apoyo_adicional = $request->input('apoyo_adicional') == '' ?
                                                        0 : $request->input('apoyo_adicional');
                    $prestamoDetalle->save();
                    $montoPrestamoAval = $prestamoDetalle->monto_aval;
                    // actualizamos los campos del aval/socio
                    //if ( $request->input('apoyo_adicional') == '' ){
                    //    $aval->update([
                    //        'monto_prestamos' => $aval->monto_prestamos + $prestamoDetalle->monto_aval,
                    //        'is_aval' => $aval->is_aval + 1,
                    //    ]);
                    //}

                    // INSERTAMOS EN LA TABLA MOVIMIENTOS
                    $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                    $nextId = $lastInsertedId + 1;
                    //$saldoAnteriroAval = ($aval->saldo - $montoPrestamoAval) + $montoPrestamoAval;
                    $saldoAnteriroAval = $aval->saldo - $aval->monto_prestamos;
                    $saldoActualAval = $saldoAnteriroAval - $request->saldo_aval[$key];
                    $movimiento = 'PRESTAMO PRE AUTORIZADO AVAL';
                    if ( $request->input('apoyo_adicional') == 1 ){
                        $movimiento = 'PRESTAMO PRE AUTORIZADO AVAL. (APOYO ADICIONAL)';
                    }
                    /*Movimiento::create([
                        'socios_id' => $request->idAval[$key],
                        'fecha' => Carbon::now(),
                        'folio' => 'MOV-' . $nextId,
                        'monto' => $request->saldo_aval[$key],
                        'saldo_anterior' => $saldoAnteriroAval,
                        'saldo_actual' => $saldoActualAval,
                        'movimiento' => $movimiento,
                        'tipo_movimiento' => 'CARGO',
                        'metodo_pago' => 'POR DEFINIR',
                        'estatus' => 'PRE-AUTORIZADO',
                    ]);*/

                    $movAval = $prestamo->movimientos()->create([
                        'socios_id'       => $request->idAval[$key],
                        'fecha'           => Carbon::now(),
                        'folio'           => 'MOV-',
                        'saldo_anterior'  => $saldoAnteriroAval,
                        'saldo_actual'    => $saldoActualAval,
                        'monto'           => $request->saldo_aval[$key],
                        'movimiento'      => $movimiento,
                        'tipo_movimiento' => 'CARGO',
                        'metodo_pago'     => 'POR DEFINIR',
                        'estatus'         => 'PRE-AUTORIZADO',
                    ]);

                    $movAval->update([
                        'folio' => 'MOV-' . $movAval->id,
                    ]);

                }
            }

            \DB::commit();
            return redirect()->route('admin.prestamos.index')->with(['id' => $id]);
        } catch (Exception $e) {
            \DB::rollback();
            //dd($e);
            $query = $e->getMessage();
            return json_encode($query);
            //return redirect()->back()
            //    ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
            //    ->withInput($request->all(), $query);
        }
    }

    public function show($id)
    {
        $prestamo = Prestamos::findorfail($id);
        if($prestamo->prestamo_especial == 0){
            $socio = Socios::findorfail($prestamo->socios_id);

            /*$prestamos = $socio->prestamos()
            ->where('debe', '>', 0)
            ->orderBy('fecha_prestamo', 'asc')
            ->sum('debe');*/

            $prestamos = $socio->prestamos()
            ->where('debe', '>', 0)
            ->where('estatus', 'AUTORIZADO')
            ->with([
                'ultimoPagoPendiente',
                'ultimaSeriePagada',
            ])
            ->withSum(['pagos as capital_pendiente' => function ($query) {
                $query->where('pagado', 0);
            }], 'capital')
            ->orderBy('fecha_prestamo', 'asc')
            ->get();

            $prestamosDetalles = $socio->prestamoDetalles()
            ->where('debe', '>', 0)
            ->sum('debe');

            $prestamoDetalle = PrestamoDetalle::join('socios', 'socios.id', '=', 'prestamo_detalles.socios_id')
            ->where('prestamos_id', $prestamo->id)
            ->select('prestamo_detalles.*', 'socios.nombre', 'socios.apellido_paterno', 'socios.apellido_materno','socios.domicilio','socios.telefono', 'socios.rfc', 'socios.saldo', 'socios.monto_prestamos')
            ->get();

            $totalCapitalPendiente = $prestamos->sum('capital_pendiente');


            $tblDos = [];
            if ($prestamo->fecha_primer_pago) {
                $fechaActual = Carbon::parse($prestamo->fecha_primer_pago);
                for ($i = 1; $i <= $prestamo->total_quincenas; $i++) {
                    if ($i == 1) {
                        // creamos la tabla
                        $tblDos[] = [
                            'Fecha_Pago' => $fechaActual->format('d/m/Y'),
                        ];
                    } else {
                        // creamos la tabla
                        if ($fechaActual->day == 15) {
                            // Si la fecha actual es el día 15, avanzamos a fin de mes
                            $fechaActual->endOfMonth();
                        } else {
                            // Si no, avanzamos 15 días
                            $fechaActual->addDays(15);
                        }
                        $tblDos[] = [
                            'Fecha_Pago' => $fechaActual->format('d/m/Y'),
                        ];
                    }
                }
            } else {
                for ($i = 1; $i <= $prestamo->total_quincenas; $i++) {
                    $tblDos[] = [
                        'Fecha_Pago' => "-",
                    ];
                }
            }

            //dd($prestamoDetalle);

            return view('prestamos.show', compact('prestamo', 'socio', 'prestamoDetalle', 'tblDos','prestamos','prestamosDetalles','totalCapitalPendiente'));
        }else if($prestamo->prestamo_especial == 1){
            $socio = Socios::findorfail($prestamo->socios_id);

            $prestamos = $socio->prestamos()
            ->where('debe', '>', 0)
            ->orderBy('fecha_prestamo', 'asc')
            ->sum('debe');

            $prestamosDetalles = $socio->prestamoDetalles()
            ->where('debe', '>', 0)
            ->sum('debe');

            $prestamoDetalle = PrestamoDetalle::join('socios', 'socios.id', '=', 'prestamo_detalles.socios_id')
                ->where('prestamos_id', $prestamo->id)
                ->select('prestamo_detalles.*', 'socios.nombre', 'socios.apellido_paterno', 'socios.apellido_materno', 'socios.rfc', 'socios.saldo', 'socios.monto_prestamos')
                ->get();


            $tblDos = [];
            if ($prestamo->fecha_primer_pago) {
                $fechaActual = Carbon::parse($prestamo->fecha_primer_pago);
                for ($i = 1; $i <= $prestamo->total_quincenas; $i++) {
                    if ($i == 1) {
                        // creamos la tabla
                        $tblDos[] = [
                            'Fecha_Pago' => $fechaActual->format('d/m/Y'),
                        ];
                    } else {
                        // creamos la tabla
                        if ($fechaActual->day == 15) {
                            // Si la fecha actual es el día 15, avanzamos a fin de mes
                            $fechaActual->endOfMonth();
                        } else {
                            // Si no, avanzamos 15 días
                            $fechaActual->addDays(15);
                        }
                        $tblDos[] = [
                            'Fecha_Pago' => $fechaActual->format('d/m/Y'),
                        ];
                    }
                }
            } else {
                for ($i = 1; $i <= $prestamo->total_quincenas; $i++) {
                    $tblDos[] = [
                        'Fecha_Pago' => "-",
                    ];
                }
            }

            return view('prestamos.show_especial', compact('prestamo', 'socio', 'prestamoDetalle', 'tblDos','prestamos','prestamosDetalles'));

        }
    }

    public function edit($id)
    {
        $prestamo = Prestamos::with([
            'socio',
            'detalles',      // PrestamoDetalle
            'detalles.socio' // Avales
        ])->findOrFail($id);

        $edit = true;

        return view('prestamos.edit', [
            'prestamo' => $prestamo,
            'socio'    => $prestamo->socio,
            'edit'     =>$edit
        ]);

        //return view('prestamos.edit', compact('prestamo'));


        $montoPrestamo = 4700; //floatval($request->input('monto_prestamo'));
        $mesesPrestamo = 18; //intval($request->input('total_quincenas'));
        //$response = $this->calcularTablaInteresUno($montoPrestamo, $mesesPrestamo);

        $response2 = $this->calcularTablaInteresDos($montoPrestamo, $mesesPrestamo);

        // Decodificar la respuesta JSON a un arreglo en PHP
        // $data = json_decode($response->getContent(), true);

        // Acceder a los valores de 'tabla_interesuno'
        /*$tablaInteresUno = $data['tabla_interesuno'];

        // Recorrer y trabajar con los valores de 'tabla_interesuno'
        $tblUno = [];
        foreach ($tablaInteresUno as $pago) {
            $numero = $pago['Pago'];
            $capital = $pago['Capital'];
            $interes = $pago['Interes'];
            $descuento = $pago['Descuento'];
            $saldoFinal = $pago['Saldo_Final'];

            // Haz lo que necesites con estos valores
            //echo "Número de Pago: $numero, Capital: $capital, Interés: $interes, Descuento: $descuento, Saldo Final: $saldoFinal <br>";
            $tblUno[] = [
                'Pago' => $numero,
                'Capital' => $capital,
                'Interes' => $interes,
                'Descuento' => $descuento,
                'Saldo_Final' => $saldoFinal,
            ];
        }

        $tblUnoJSON = json_encode($tblUno);
        echo $tblUnoJSON;*/

        //dd($response);

        /*$saldo = $montoPrestamo;
        $totalcapint = 0;

        $pagos = [];
        $totalinteres = 0;
        $pagomonto = 0;
        $interes = 0;
        $pagomontored = 0;
        $interesred = 0;
        $capinter = 0;
        $saldofinal = 0;
        $pagomonto = $montoPrestamo / $mesesPrestamo;
        for ($i = 1; $i <= $mesesPrestamo; $i++) {
            //if ($montoPrestamo >= $pagomonto) {
            //    $pagomontored = $this->round($pagomonto);
            //} else {
            //    $pagomontored = $this->round($montoPrestamo);
            //}

            $pagomontored = ($montoPrestamo >= $pagomonto) ? $this->round($pagomonto) : $this->round($montoPrestamo);

            $interes = ($montoPrestamo / 100) * 1.5; // TASA DE INTERES
            $interesred = $this->round1($interes);
            $capinter = $pagomontored + $interesred;
            $totalinteres +=  $interesred;
            $totalcapint = $totalcapint + $capinter;
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
            'total_interes' => $totalinteres,
            'total_capital_interes' => $totalinteres + $saldo,
        ]);*/
    }

    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();

            $prestamo = Prestamos::with('detalles')->findOrFail($id);
            $socio = Socios::findOrFail($prestamo->socios_id);

            // 1. CANCELAR MOVIMIENTOS VIEJOS
            //Movimiento::where('prestamos_id', $prestamo->id)
            //->where('estatus', 'PRE-AUTORIZADO')
            //->update([
            //    'estatus' => 'CANCELADO',
            //    'movimiento' => DB::raw(
            //        "CONCAT(movimiento,' (CANCELADO POR ACTUALIZACIÓN)')"
            //    )
            //]);

            // 2. ELIMINAR DETALLES (SE RECREAN)
            PrestamoDetalle::where('prestamos_id', $prestamo->id)->delete();

            $fecha = Carbon::createFromFormat('d/m/Y', $request->fecha_primer_pago)->format('Y-m-d');

            $saldoSocio = $request->apoyo_adicional == 1
                ? $request->saldo_socio - 5000
                : $request->saldo_socio;

            $prestamo->update([
                'monto_prestamo'     => $request->monto_prestamo,
                'total_intereses'    => $request->prestamo_intereses - $request->monto_prestamo,
                'pago_quincenal'     => $request->pago_quincenal,
                'total_quincenas'    => $request->total_quincenas,
                'debia'              => $request->prestamo_intereses,
                'debe'               => $request->prestamo_intereses,
                'diferencia'         => $saldoSocio,
                'fecha_primer_pago'  => $fecha,
                'proximo_pago'       => $fecha,
                'folio'              => $request->folio,
                'num_nomina'         => $request->num_nomina,
                'num_empleado'       => $request->num_empleado,
                'apoyo_adicional'    => $request->apoyo_adicional ?? 0,
                'documentacion'      => [
                    'copia_talon'      => $request->has('copia_talon'),
                    'copia_ine'        => $request->has('copia_ine'),
                    'credencial_socio' => $request->has('credencial_socio'),
                    'pagare'           => $request->has('pagare'),
                    'solicitud'        => $request->has('solicitud'),
                ],
            ]);

            //4. NUEVO MOVIMIENTO SOCIO
            $lastId = Movimiento::max('id') ?? 0;

            // INSERTAMOS EN LA TABLA MOVIMIENTOS
            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
            $nextId = $lastInsertedId + 1;

            Movimiento::create([
                'socios_id' => $prestamo->socios_id,
                'fecha' => now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $socio->saldo,
                'saldo_actual' => $socio->saldo - $saldoSocio,
                'monto' => $saldoSocio,
                'movimiento' => 'PRESTAMO PRE AUTORIZADO (ACTUALIZADO)',
                'tipo_movimiento' => 'CARGO',
                'metodo_pago' => 'POR DEFINIR',
                'estatus' => 'PRE-AUTORIZADO',
            ]);

            //5. RECREAR AVALES + MOVIMIENTOS
            if ($request->filled('idAval')) {
                foreach ($request->idAval as $i => $avalId) {

                    $aval = Socios::findOrFail($avalId);

                    PrestamoDetalle::create([
                        'prestamos_id' => $prestamo->id,
                        'socios_id' => $avalId,
                        'aval' => $aval->nombre_completo,
                        'num_aval' => $aval->num_socio,
                        'monto_socio' => $saldoSocio,
                        'monto_aval' => $request->saldo_aval[$i],
                        'debia' => $request->saldo_aval[$i],
                        'debe' => $request->saldo_aval[$i],
                        'apoyo_adicional' => $request->apoyo_adicional ?? 0,
                    ]);

                    //$lastId++;

                    $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                    $nextId = $lastInsertedId + 1;

                    Movimiento::create([
                        'prestamos_id'   => $prestamo->id,
                        'socios_id'      => $avalId,
                        'fecha'          => now(),
                        'folio'          => 'MOV-' . $nextId,
                        'saldo_anterior' => $aval->saldo,
                        'saldo_actual'   => $aval->saldo - $request->saldo_aval[$i],
                        'monto'          => $request->saldo_aval[$i],
                        'movimiento'     => 'PRESTAMO PRE AUTORIZADO AVAL (ACTUALIZADO)',
                        'tipo_movimiento'=> 'CARGO',
                        'metodo_pago'    => 'POR DEFINIR',
                        'estatus'        => 'PRE-AUTORIZADO',
                    ]);
                }
            }

            \DB::commit();
            //return redirect()->route('prestamos.index')->with('success', 'Préstamo actualizado correctamente');
            return redirect()->route('admin.prestamos.index')->with(['id' => $id]);

        } catch (\Exception $e) {
            \DB::rollBack();
            dd($e->getMessage());
            return back()->withErrors($e->getMessage());
        }
    }

    public function destroy(Prestamos $prestamos)
    {
        //
    }

    public function detalleSocio(Request $request)
    {
        // Obtengo los datos del socio para su solicitud de prestamo.
        if ($request->input('socios_id')) {

            $socio = Socios::findorfail($request->input('socios_id'));

            $dataDetalle = array(
                'id' => $socio->id,
                'num_socio' => $socio->num_socio,
                'nombre_completo' => $socio->nombre_completo,
                'rfc' => $socio->rfc,
                'fecha_alta' => $socio->fecha_alta,
                'saldo' => $socio->saldo,
                'saldo_disponible' => $socio->saldo - $socio->monto_prestamos,
                'numero_prestamos' => $socio->numero_prestamos,
            );

            return json_encode($dataDetalle);
        }
        return json_encode('pilin');
    }

    public function detalleAval(Request $request)
    {
        // Obtengo los datos del socio para su solicitud de prestamo.
        if ($request->input('socios_id') && $request->input('tipo') == 'socios') {

            $socios = Socios::selectRaw('*, "socios" as tipo')
                ->whereRaw('saldo - monto_prestamos > 0')
                ->where('activo', 1)
                ->whereNotIn('id', [$request->input('socios_id')]) // Excluir el ID seleccionado
                ->orderBy('nombre_completo', 'asc')
                ->get();

            return json_encode($socios);
        }

        if ($request->input('socios_id') && $request->input('tipo') == 'aval') {

            $socio = Socios::findorfail($request->input('socios_id'));

            $dataDetalle = array(
                'id' => $socio->id,
                'num_socio' => $socio->num_socio,
                'nombre_completo' => $socio->nombre_completo,
                'nombre' => $socio->nombre_completo,
                'apellido_paterno' => $socio->apellido_paterno,
                'apellido_materno' => $socio->apellido_materno,
                'rfc' => $socio->rfc,
                'fecha_alta' => $socio->fecha_alta,
                'saldo' => $socio->saldo,
                'saldo_disponible' => $socio->saldo - $socio->monto_prestamos,
                'numero_prestamos' => $socio->numero_prestamos,
                'is_aval' => $socio->is_aval,
                'tipo' => 'aval',
            );

            return json_encode($dataDetalle);
        }

        return json_encode('pilin');
    }

    public function allSocios(Request $request)
    {
        /*$socios = Socios::where('activo', 1)
            ->whereRaw(' (saldo + 500) - monto_prestamos > 0')
            ->orderBy('nombre_completo', 'asc')
            ->get();

        return json_encode($socios);*/

        $search = $request->input('search');

        $socios = Socios::where('activo', 1)
        ->whereRaw('COALESCE(saldo + 0, 0) - COALESCE(monto_prestamos, 0) > 0') // Si hay valores nulos, podría ser problemático. En ese caso, podrías utilizar una función de SQL como COALESCE para evitar errores
        ->when($search, function($query, $search) {
            return $query->where('nombre_completo', 'like', "%{$search}%");
        })
        ->orderBy('nombre_completo', 'asc')
        ->limit(10)
        ->get();

        return response()->json($socios);
    }

    public function reciboPrestamo($id)
    {
        $prestamo = Prestamos::findorfail($id);
        $socio = Socios::findorfail($prestamo->socios_id);
        $prestamoDetalle = PrestamoDetalle::join('socios', 'socios.id', '=', 'prestamo_detalles.socios_id')
            ->where('prestamos_id', $prestamo->id)
            ->select('prestamo_detalles.*', 'socios.nombre', 'socios.apellido_paterno', 'socios.apellido_materno', 'socios.rfc', 'socios.saldo', 'socios.monto_prestamos')
            ->get();

        //  - CREAMOS EL PDF ----
        //return view('recibos.recibo_prestamos', compact('prestamo','socio','prestamoDetalle'));
        $pdf = PDF::loadView('recibos.recibo_prestamos', compact('prestamo', 'socio', 'prestamoDetalle'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream();
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
            /*if ($montoPrestamo >= $pagomonto) {
                $pagomontored = $this->round($pagomonto);
            } else {
                $pagomontored = $this->round($montoPrestamo);
            }*/

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

    public function calcularTablaInteresDos($montoPrestamo, $mesesPrestamo)
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

        //$tablaInteresUno = $this->calcularTablaInteresUno($montoprest, $mesesprest);
        // Llamar a calcularTablaInteresUno y obtener la respuesta JSON
        $response = $this->calcularTablaInteresUno($montoprest, $mesesprest);
        // Decodificar la respuesta JSON en un arreglo en PHP
        $data = json_decode($response->getContent(), true);
        // Acceder a los valores de 'tabla_interesuno' en el arreglo $data
        $tablaInteresUno = $data['tabla_interesuno'];
        $tblDos = [];
        for ($i = 1; $i <= $mesesprest; $i++) {
            if ($i == 1) {
                $pagouno1 = self::$totalcapint / $mesesprest;
                $pagounost = number_format($pagouno1, 1); // pagouno1.toFixed(1);
                $pagouno = floatval($pagounost);
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
                // $saldofinald_final = (float) $saldofinald_formateado;
                //// FIN redondeando la variable $saldofinald

                /*echo "<br/>";
                echo "montoprest: ".$montoprest;
                echo "<br/>";
                echo "capital: ".$capital;
                echo "<br/>";
                echo "saldofinald(1-1): ".$saldofinald;
                echo "<br/>";
                echo "saldofinald(1): ".number_format($saldofinald, 1);
                echo "<br/>";
                echo "saldofinald_final: ".$saldofinald_final;
                echo "<br/>";*/
                // creamos la tabla
                $tblDos[] = [
                    'Pago' => $i,
                    'Capital' => number_format($capital, 1),
                    'Interes' => $interesado,
                    'Descuento' => number_format($pagouno, 1),
                    //'Saldo_Final' => number_format($saldofinald, 1),
                    'Saldo_Final' => number_format($saldofinald_final, 1),
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
                    //$capital = (self::$totalcapint / $mesesprest) - $(this).find("td").eq(2).text();
                    $capital = (self::$totalcapint / $mesesprest) - $elemento['Interes'];
                    if ($contador == 2) {
                        //dd(number_format(floatval($valorColumna4)));
                        //dd(number_format(floatval($capital)));
                        $numeroCol4 = $valorColumna4;
                        $numeroCol4 = str_replace(",", "", $numeroCol4); // Eliminar la coma
                        $numeroCol4 = floatval($numeroCol4);

                        //dd($numeroCol4);
                        //dd($capital);

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

                        /* echo "<br/>";
                        echo "numeroCol4: ".$numeroCol4;
                        echo "<br/>";
                        echo "capital: ".$capital;
                        echo "<br/>";
                        echo "saldofinald(2-2): ".$saldofinald;
                        echo "<br/>";
                        echo "saldofinald(2): ".number_format($saldofinald, 1);
                        echo "<br/>";
                        echo "saldofinald_final: ".$saldofinald_final;
                        echo "<br/>";*/
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

                        /*echo "<br/>";
                        echo "valorAnteriorSaldofinald: ".floatval($valorAnteriorSaldofinald);
                        echo "<br/>";
                        echo "capital: ".floatval($capital);
                        echo "<br/>";
                        echo "saldofinald(3-3): ".$saldofinald;
                        echo "<br/>";
                        echo "saldofinald(3): ".number_format($saldofinald,1);
                        echo "<br/>";
                        echo "saldofinald_final: ".$saldofinald_final;
                        echo "<br/>";*/
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

                    $tblDos[] = [
                        'Pago' => $contador,
                        'Capital' => number_format($capital, 1),
                        'Interes' => $interesado,
                        'Descuento' => number_format($pagouno, 1),
                        //'Saldo_Final' => number_format(floatval($saldofinald), 1),//number_format($saldofinald, 1),
                        'Saldo_Final' => number_format(floatval($saldofinald_final), 1), //number_format($saldofinald, 1),
                    ];
                    $valorAnteriorSaldofinald = $saldofinald_final; //$saldofinald;
                    $capitalAnterior = number_format($capital, 1); //$capital.toFixed(1);
                    $contador++;
                }
                //$("#tabla-interesuno tbody tr").slice(1).each(function() {

                //});
                $primerIteracion = false;
            }
        }
        return response()->json([
            'tabla_interesdos' => $tblDos,
        ]);







        /*$montoprest = $montoPrestamo;
        $mesesprest = $mesesPrestamo;
        self::$totalcapint = 0;

        //$tablaInteresUno = $this->calcularTablaInteresUno($montoprest, $mesesprest);
        // Llamar a calcularTablaInteresUno y obtener la respuesta JSON
        $response = $this->calcularTablaInteresUno($montoprest, $mesesprest);
        // Decodificar la respuesta JSON en un arreglo en PHP
        $data = json_decode($response->getContent(), true);
        // Acceder a los valores de 'tabla_interesuno' en el arreglo $data
        $tablaInteresUno = $data['tabla_interesuno'];

        $tblDos = [];

        $primerIteracion = true;
        $valorColumna4 = 0;
        $valorAnteriorSaldofinald = 0;
        $capitalAnterior = 0;

        self::$interesid = 0;
        for ($i = 1; $i <= $mesesprest; $i++) {
            if ($i == 1) {
                $pagouno1 = self::$totalcapint / $mesesprest;
                $pagounost = number_format($pagouno1, 1);
                $pagouno = floatval($pagounost);
                $primerElemento = $tablaInteresUno[0];
                $interesado = $primerElemento['Interes'];
                $interesValor = floatval($interesado);
                self::$interesid += $interesValor;
                $capital = (self::$totalcapint / $mesesprest) - $interesValor;
                $saldofinald = $montoprest - $capital;

                $tblDos[] = [
                    'Pago' => $i,
                    'Capital' => number_format($capital, 1),
                    'Interes' => $interesado,
                    'Descuento' => number_format($pagouno, 1),
                    'Saldo_Final' => number_format($saldofinald, 1),
                ];

                $capital = $pagouno - self::$interesid;
                $valorColumna4 = number_format($saldofinald, 1);
            } else if ($primerIteracion) {
                self::$interesid = 0;
                $contador = 2;
                foreach ($tablaInteresUno as $elemento) {
                    $interesado = $elemento['Interes'];
                    $interesValor = floatval($interesado);
                    self::$interesid += $interesValor;
                    $capital = (self::$totalcapint / $mesesprest) - $interesValor;

                    if ($contador == 1) {
                        //$saldofinald = number_format($valorColumna4 - $capital, 1);
                        $saldofinald = number_format(floatval($valorColumna4) - floatval($capital), 1);
                        $valorAnteriorSaldofinald = $saldofinald;
                    } else {
                        //$saldofinald = number_format($valorAnteriorSaldofinald - $capital, 1);
                        $saldofinald = number_format(floatval($valorAnteriorSaldofinald) - floatval($capital), 1);
                        if ($contador < $mesesprest) {
                            $capital = (self::$totalcapint / $mesesprest) - $interesValor;
                        } else if ($contador == $mesesprest) {
                            //$capital = number_format($valorAnteriorSaldofinald, 1);
                            $capital = number_format(floatval($valorAnteriorSaldofinald) , 1);
                            $pagouno = floatval($capital) + $interesValor;
                            $saldofinald = 0;
                        }
                    }

                    $tblDos[] = [
                        'Pago' => $contador,
                        'Capital' => number_format(floatval($capital), 1),
                        'Interes' => $interesado,
                        'Descuento' => number_format(floatval($pagouno), 1),
                        'Saldo_Final' => number_format(floatval($saldofinald), 1),
                    ];

                    $valorAnteriorSaldofinald = $saldofinald;
                    $capitalAnterior = number_format($capital, 1);
                    $contador++;
                }
                $primerIteracion = false;
            }
        }

        return response()->json([
            'tabla_interesdos' => $tblDos,
        ]);*/
    }

    public function validaAval(Request $request)
    {
        // Obtener el usuario por su nombre (name)
        $user = User::where('name', $request->input('aval'))->first();

        if ($user && Hash::check($request->input('pass'), $user->password)) {
            // La contraseña es correcta y el usuario está autenticado
            return response()->json(['estado' => 'aprobado']);
        }

        // La contraseña es incorrecta o el usuario no existe, mostrar un mensaje de error
        return response()->json('invalido', 422);
    }

    public function autoriza3prestamo(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        //if (!$user || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
        //    return response()->json(['autorizado' => false], 401);
        //}

        if (!$user || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            return response()->json(['autorizado' => false, 'mensaje' => 'Credenciales inválidas'], 401);
        }

        // Verifica si el usuario tiene el rol ADMINISTRADOR
        if (!$user->hasRole('ADMINISTRADOR')) {
            return response()->json(['autorizado' => false, 'mensaje' => 'No tiene permisos suficientes'], 403);
        }


        return response()->json(['autorizado' => true]);
    }
}
