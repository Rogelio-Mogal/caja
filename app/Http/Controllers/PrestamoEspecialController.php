<?php

namespace App\Http\Controllers;

use App\Models\Prestamos;
use App\Models\PrestamoDetalle;
use App\Models\PrestamosConceptos;
use App\Models\Socios;
use App\Models\Movimiento;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class PrestamoEspecialController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:crear-prestamos-especiales', ['only'=>['create', 'store']]);

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
        $prestamoEspecial = PrestamosConceptos::where('activo', 1)
        ->whereRaw('disponibles > 0')
        ->get();

        return view('prestamos_especiales.create', compact('prestamo','prestamoEspecial'));
    }

    public function store(Request $request)
    {
        //dd($request);
        $prestamoConcepto = PrestamosConceptos::where('id', $request->input('conceptoId'))->get()->first();
        if( $prestamoConcepto->disponibles == 0 ){
            return redirect()->back()
                ->with(['advertencia' => 'No hay piezas disponibles']);
        }
        try {
            \DB::beginTransaction();
            
            $socio = Socios::findorfail($request->input('socios_id'));
            $prestamo = new Prestamos();
            $fecha = $request->input('fecha_primer_pago');
            $nuevaFecha = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            $saldoSocio = $request->input('apoyo_adicional') == '' ? 
                $request->input('saldo_socio') : 
                $request->input('saldo_socio') - 5000;

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
            $prestamo->diferencia = 0; //$saldoSocio;
            $prestamo->fecha_primer_pago = $nuevaFecha;
            $prestamo->folio = $request->input('folio');
            $prestamo->num_nomina = $request->input('num_nomina');
            $prestamo->num_empleado = $request->input('num_empleado');
            //$prestamo->fecha_primer_corte = $request->input('fecha_primer_corte');
            $prestamo->apoyo_adicional = $request->input('apoyo_adicional') == '' ? 
                                        0 : $request->input('apoyo_adicional');
            $prestamo->prestamo_especial = 1;
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

            // Actualizamos el número del prestamo especial
            $prestamoConcepto = PrestamosConceptos::where('id', $request->input('conceptoId'))->get()->first();
            $prestamoConcepto->update([
                'disponibles' => ($prestamoConcepto->disponibles - 1),
            ]);
            

            // actualizamos los campos del socio
            /*$socio->update([
                'monto_prestamos' => $socio->monto_prestamos + $saldoSocio,
                'numero_prestamos' => $socio->numero_prestamos + 1,
            ]);*/

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
                'movimiento' => 'PRESTAMO PRE AUTORIZADO. '. $request->input('concepto'),
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
                'movimiento'      => 'PRESTAMO PRE AUTORIZADO. '. $request->input('concepto'),
                'tipo_movimiento' => 'CARGO',
                'metodo_pago'     => 'POR DEFINIR',
                'estatus'         => 'PRE-AUTORIZADO',
            ]);

            $movSocio->update([
                'folio' => 'MOV-' . $movSocio->id,
            ]);

            \DB::commit();
            return redirect()->route('admin.prestamos.index')->with(['id_especial' => $id]);
        } catch (Exception $e) {
            \DB::rollback();
            //dd($e);
            $query = $e->getMessage();
            //return json_encode($query);
            return redirect()->back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }

    public function allSocios(Request $request)
    {
        /*
        $socios = Socios::where('activo', 1)
            //->whereRaw(' (saldo + 500) - monto_prestamos > 0')
            ->orderBy('nombre_completo', 'asc')
            ->get();

        return json_encode($socios);
        */

        $search = $request->input('search');

        $socios = Socios::where('activo', 1)
        ->when($search, function($query, $search) {
            return $query->where('nombre_completo', 'like', "%{$search}%");
        })
        ->orderBy('nombre_completo', 'asc')
        ->limit(10)
        ->get();

        return response()->json($socios);
    }
}
