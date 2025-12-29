<?php

namespace App\Http\Controllers;

use App\Models\Ahorros;
use App\Models\Socios;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use PDF;

class AhorrosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:agregar-ahorro-voluntario', ['only'=>['index','create', 'store']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        $ahorros = Ahorros::join('socios', 'ahorros.socios_id', '=', 'socios.id')
            ->where('ahorros.activo',1)
            ->get(['ahorros.*', 'socios.nombre']);

        //dd($ahorros);

        return view('ahorro.index', compact('ahorros'));
    }

    public function create()
    {
        $ahorro = new Ahorros;
        $socios = Socios::all();

        return view('ahorro.create', compact('ahorro', 'socios'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction(); // Iniciar la transacción

            $ahorro = new Ahorros();
            $ahorro->socios_id = $request->input('socios_id');
            $ahorro->fecha_ahorro = Carbon::now();
            $ahorro->monto = $request->input('monto');
            $ahorro->metodo_pago = $request->input('metodo_pago');
            $ahorro->referencia = $request->input('referencia');
            $ahorro->is_aportacion = $request->has('is_aportacion') ? 1 : 0;
            $ahorro->save();

            $socio = Socios::find($ahorro->socios_id);
            $saldoAnteriro = $socio->saldo;
            $socio->saldo = $socio->saldo + $ahorro->monto;
            // Solo cambiar a ACTIVO si actualmente es PENDIENTE y es aportación
            if ($socio->tipo === 'PENDIENTE' && $ahorro->is_aportacion == 1) {
                $socio->tipo = 'ACTIVO';

                $socio->ajustes()->create([
                    'tipo' => 'ACTIVO',
                    'fecha' => $request->input('fecha_baja') ?? now(),
                    'observaciones' => $request->input('observaciones') ?? 'Aportacion social, socio activo.',
                    'wci' => auth()->id(),
                ]);
            }
            $socio->save();

            // Obtener el último ID insertado en la tabla de movimientos
            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;

            // Inserta los registros en la tabla movimientos

            $nextId = $lastInsertedId + 1;

            /*Movimiento::create([
                'socios_id' => $socio->id,
                'fecha' => Carbon::now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $socio->saldo,
                'monto' => $ahorro->monto,
                //'movimiento' => 'AHORRO VOLUNTARIO',
                'movimiento' => $ahorro->is_aportacion ? 'APORTACIÓN SOCIAL' : 'AHORRO VOLUNTARIO',
                'tipo_movimiento' => 'ABONO', //cargo-abono
                'metodo_pago' => $request->input('metodo_pago'),
                'estatus' => 'EFECTUADO',
            ]);*/

            $ahorro->movimientos()->create([
                'socios_id' => $socio->id,
                'fecha' => now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $socio->saldo,
                'monto' => $ahorro->monto,
                'movimiento' => $ahorro->is_aportacion
                    ? 'APORTACIÓN SOCIAL'
                    : 'AHORRO VOLUNTARIO',
                'tipo_movimiento' => 'ABONO',
                'metodo_pago' => $request->input('metodo_pago'),
                'estatus' => 'EFECTUADO',
            ]);


            DB::commit();
            return redirect()->route('admin.socios.index')->with(['id' => $ahorro->id]);
        } catch (Exception $e) {
            DB::rollback(); // Revertir la transacción en caso de error
            $query = $e->getMessage();
            // return json_encode($query);
            return redirect()->back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show($id)
    {
        $ahorro = Ahorros::join('socios', 'ahorros.socios_id', '=', 'socios.id')
            ->select('ahorros.*', 'socios.nombre_completo')
            ->where('ahorros.id', $id)
            ->first();

        return json_encode($ahorro);
    }

    public function edit(Ahorros $ahorros)
    {
        //
    }

    public function update(Request $request, Ahorros $ahorros)
    {
        //
    }

    public function destroy(Request $request,$id)
    {
        try {
            \DB::beginTransaction();

            $ahorro = Ahorros::findorfail($request->input('id'));
            $socio = Socios::findorfail($ahorro->socios_id);

             // Validar saldo suficiente
            if ($socio->saldo < $ahorro->monto) {
                return response()->json([
                    'error' => 'No es posible cancelar el ahorro. Hubo movimientos posteriores y el saldo no cubre el monto.'
                ], 422);
            }

            $saldoAnteriro = $socio->saldo;
            $saldoActual = $saldoAnteriro - $ahorro->monto;

            // Marcar el ahorro como cancelado
            $ahorro->motivo_cancelacion = nl2br($request->input('comentarios')).". ".Carbon::now();
            $ahorro->activo = 0;
            $ahorro->save();

            // Actualizar saldo del socio
            $socio->update([
                'saldo' => $saldoActual,
            ]);

            // INSERTAMOS EN LA TABLA MOVIMIENTOS
            $nextId = Movimiento::max('id') + 1;
            /*Movimiento::create([
                'socios_id' => $ahorro->socios_id,
                'fecha' => Carbon::now(),
                'folio' => 'MOV-' . $nextId,
                'monto' => $ahorro->monto,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $saldoActual,
                'movimiento' => 'AHORRO CANCELADO',
                'tipo_movimiento' => 'CARGO',
                'metodo_pago' => 'EFECTIVO',
                'estatus' => 'CANCELADO',
            ]);*/

            $ahorro->movimientos()->create([
                'socios_id' => $ahorro->socios_id,
                'fecha' => now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $saldoActual,
                'monto' => $ahorro->monto,
                'movimiento' => 'AHORRO CANCELADO',
                'tipo_movimiento' => 'CARGO',
                'metodo_pago' => 'EFECTIVO',
                'estatus' => 'CANCELADO',
            ]);


            DB::commit();
            return json_encode($ahorro->id);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'error' => 'Hubo un error durante el proceso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reciboAhorroVoluntario($id)
    {
        $ahorro = Ahorros::select(
            'ahorros.*',
            'socios.num_socio',
            'socios.nombre_completo',
            'socios.saldo'
        )
        ->join('socios','ahorros.socios_id','=','socios.id')
        ->where('ahorros.id','=',$id)
        ->first();
        //->get();

        //  - CREAMOS EL PDF ----
        $pdf = PDF::loadView('recibos.recibo_ahorro_voluntario', compact('ahorro'))
            ->setPaper('letter', 'portrait');
        return $pdf->stream();
    }

    public function allSocios()
    {
        $socios = Socios::where('activo', 1)
            ->orderBy('nombre_completo', 'asc')
            ->get();

        return json_encode($socios);
    }
}
