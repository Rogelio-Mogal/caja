<?php

namespace App\Http\Controllers;

use App\Models\DevolucionEfectivo;
use App\Models\Socios;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use PDF;

class DevolucionEfectivoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:devoluciones', ['only'=>['index','create', 'store','show','update']]);

    }

    public function index()
    {
        $devolucion = DevolucionEfectivo::with('socio')->get();

        return view('devoluciones.index', compact('devolucion'));
    }

    public function create()
    {
        $devolucion = new DevolucionEfectivo;
        $socios = Socios::all();

        return view('devoluciones.create', compact('devolucion', 'socios'));
    }

    public function store(Request $request)
    {
        try {
            $devolucion = new DevolucionEfectivo();
            $devolucion->socios_id = $request->input('socios_id');
            $devolucion->fecha_captura = Carbon::now();
            $devolucion->importe = $request->input('importe');
            $devolucion->forma_pago = $request->input('forma_pago');
            $devolucion->referencia = $request->input('referencia');
            $devolucion->nota = $request->input('nota');
            $devolucion->estatus = 'SOLICITADO';
            $devolucion->wci = auth()->user()->id;
            $devolucion->save();

            return redirect()->route('admin.devoluciones.index')->with(['id' => $devolucion->id]);
        } catch (Exception $e) {
            $query = $e->getMessage();
            // return json_encode($query);
            return redirect()->back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show($id)
    {

        $devolucion = DevolucionEfectivo::with('socio')->find($id);
        
        return json_encode($devolucion);
    }

    public function edit(DevolucionEfectivo $devolucionEfectivo)
    {
        //
    }

    public function update(Request $request)
    {
        try {
            $devolucion = DevolucionEfectivo::findorfail($request->input('id'));
            //$devolucion->fecha_devolucion = Carbon::createFromFormat('d/m/Y', $request->input('fecha_primer_pago'))
            //->setTimeFrom(Carbon::now()) // copia la hora actual
            //->format('Y-m-d H:i:s');
            $devolucion->fecha_devolucion = Carbon::createFromFormat('d/m/Y', $request->input('fecha_primer_pago'), 'America/Mexico_City')
            ->setTimeFrom(Carbon::now('America/Mexico_City'))
            ->format('Y-m-d H:i:s');
            $devolucion->forma_pago = $request->input('forma_pago');
            $devolucion->nota = $devolucion->nota." / ".$request->input('nota');
            $devolucion->estatus = 'AUTORIZADO';
            $devolucion->save();

            return json_encode($devolucion->id);
        } catch (\Exception $e) {
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function destroy(Request $request,$id)
    {
        try {
            $devolucion = DevolucionEfectivo::findorfail($request->input('id'));

            $devolucion->nota = $devolucion->nota." / ".nl2br($request->input('comentarios')).". ".Carbon::now();
            $devolucion->activo = 0;
            $devolucion->estatus = 'CANCELADO';
            $devolucion->save();

            return json_encode($devolucion->id);
        } catch (\Exception $e) {
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function reciboDevolucion($id)
    {
        
        $devolucion = DevolucionEfectivo::with('socio')->find($id);

        //  - CREAMOS EL PDF ----
        $pdf = PDF::loadView('recibos.recibo_devolucion', compact('devolucion'))
            ->setPaper('letter', 'portrait');
        return $pdf->stream();
    }
}
