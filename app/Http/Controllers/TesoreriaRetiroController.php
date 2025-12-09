<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Retiro;
use App\Models\Socios;
use App\Models\Movimiento;
use Carbon\Carbon;
use DB;
use PDF;

class TesoreriaRetiroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:finalizar-retiro', ['only'=>['index','create', 'store','edit', 'update','destroy']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/

    }

    public function index()
    {
        $retiro = Retiro::join('socios', 'retiros.socios_id', '=', 'socios.id')
            ->where('estatus', '=', 'PRE-AUTORIZADO')
            ->get(['retiros.*', 'socios.nombre_completo']);
        return view('tesoreria_retiro.index', compact('retiro'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            $retiro = Retiro::findorfail($request->input('id'));
            $socio = Socios::findorfail($retiro->socios_id);
            //$saldoAnteriro = $socio->saldo + $retiro->monto_retiro;
            //$saldoActual = ($socio->saldo + $retiro->monto_retiro) - $request->input('saldo_aprobado');

            $saldoAnteriro = $socio->saldo;
            $saldoActual = ($socio->saldo) - $request->input('saldo_aprobado');

            $retiro->fecha_retiro = Carbon::now();
            $retiro->saldo_aprobado = $request->input('saldo_aprobado');
            $retiro->forma_pago = $request->input('forma_pago');
            $retiro->comentarios = '-';
            $retiro->estatus = 'AUTORIZADO';
            $retiro->save();

            // Actualizamos el saldo del socio
            //$nuevoSaldo = $socio->saldo + $retiro->monto_retiro - $retiro->saldo_aprobado;
            $nuevoSaldo = $socio->saldo - $retiro->saldo_aprobado;
            $socio->update(['saldo' => $nuevoSaldo]);

            // Actualizamos el campo saldo_actual en el modelo Retiro
            $retiro->saldo_actual = $nuevoSaldo;
            $retiro->save();

            // INSERTAMOS EN LA TABLA MOVIMIENTOS
            $nextId = Movimiento::max('id') + 1;
            Movimiento::create([
                'socios_id' => $retiro->socios_id,
                'fecha' => Carbon::now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $saldoActual,
                'monto' => $retiro->saldo_aprobado,
                'movimiento' => 'RETIRO',
                'tipo_movimiento' => 'CARGO',
                'metodo_pago' => $retiro->forma_pago,
                'estatus' => 'AUTORIZADO',
            ]);

            DB::commit();
            //return redirect()->route('admin.tesoreria.retiro.index')->with(['id' => $retiro->id]);
            return json_encode($retiro->id);
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
        $retiro = Retiro::join('socios', 'retiros.socios_id', '=', 'socios.id')
            ->select('retiros.*','socios.nombre_completo')
            ->where('retiros.id', $id)
            ->get();

        return json_encode($retiro);
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

            $retiro = Retiro::findorfail($request->input('id'));
            $socio = Socios::findorfail($retiro->socios_id);
            $saldoAnteriro = $socio->saldo;
            $saldoActual = $socio->saldo + $retiro->monto_retiro;


            $retiro->comentarios = nl2br($request->input('comentarios')).". ".Carbon::now();
            $retiro->estatus = 'CANCELADO';
            $retiro->save();

            // Actualizamos el saldo del socio
            $socio->update(['saldo' => $socio->saldo + $retiro->monto_retiro]);

            // INSERTAMOS EN LA TABLA MOVIMIENTOS
            $nextId = Movimiento::max('id') + 1;
            Movimiento::create([
                'socios_id' => $retiro->socios_id,
                'fecha' => Carbon::now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $saldoActual,
                'monto' => $retiro->monto_retiro,
                'movimiento' => 'RETIRO',
                'tipo_movimiento' => 'ABONO',
                'metodo_pago' => 'EFECTIVO',
                'estatus' => 'CANCELADO',
            ]);

            DB::commit();
            //return redirect()->route('admin.tesoreria.retiro.index')->with(['id' => $retiro->id]);
            //return redirect()->route('admin.tesoreria.retiro.index')->with('message', 'exito');
            return json_encode($retiro->id);
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

    public function reciboRetiro($id)
    {
        $retiro = Retiro::select(
            'retiros.*',
            'socios.num_socio',
            'socios.nombre_completo',
            'socios.saldo'
        )
        ->join('socios','retiros.socios_id','=','socios.id')
        ->where('retiros.id','=',$id)
        ->first();

        //  - CREAMOS EL PDF ----
        $pdf = PDF::loadView('recibos.recibo_retiro_aprobado', compact('retiro'))
            ->setPaper('letter', 'portrait');
        return $pdf->stream();
    }
}
