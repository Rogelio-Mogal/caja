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
use Illuminate\Support\Facades\Hash;

class PrestamoEnfermedadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:crear-prestamos-enfermedad', ['only'=>['create', 'store']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        //
    }

    public function create()
    {
        $prestamo = new Prestamos;

        return view('prestamos_enfermedad.create', compact('prestamo'));
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
            //$prestamo->num_nomina = $request->input('num_nomina');
            //$prestamo->num_empleado = $request->input('num_empleado');
            //$prestamo->fecha_primer_corte = $request->input('fecha_primer_corte');
            $prestamo->apoyo_adicional = $request->input('apoyo_adicional') == '' ? 
                                        0 : $request->input('apoyo_adicional');
            $prestamo->prestamo_enfermedad = 1;
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
            //    'monto_prestamos' => $socio->monto_prestamos + $saldoSocio,
            //    'numero_prestamos' => $socio->numero_prestamos + 1,
            //]);

            // INSERTAMOS EN LA TABLA MOVIMIENTOS
            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
            $nextId = $lastInsertedId + 1;
            $saldoAnteriro = ($socio->saldo - $montoPrestamo) + $montoPrestamo;
            $saldoActual = $saldoAnteriro - $saldoSocio;

            Movimiento::create([
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
                    $prestamoDetalle->num_nomina = $aval->id;
                    $prestamoDetalle->num_empleado = $aval->num_socio;
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
                    $saldoAnteriroAval = ($aval->saldo - $montoPrestamoAval) + $montoPrestamoAval;
                    $saldoActualAval = $saldoAnteriroAval - $request->saldo_aval[$key];
                    $movimiento = 'PRESTAMO PRE AUTORIZADO AVAL';
                    if ( $request->input('apoyo_adicional') == 1 ){
                        $movimiento = 'PRESTAMO PRE AUTORIZADO AVAL. (APOYO ADICIONAL)';
                    }
                    Movimiento::create([
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
                    ]);
                }
            }

            \DB::commit();
            return redirect()->route('admin.prestamos.index')->with(['id' => $id]);
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

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
