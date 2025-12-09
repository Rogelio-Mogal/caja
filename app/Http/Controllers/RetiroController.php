<?php

namespace App\Http\Controllers;

use App\Models\Retiro;
use App\Models\Socios;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PDF;

class RetiroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:aprobar-retiro', ['only'=>['index','create','store']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        $retiro = Retiro::join('socios', 'retiros.socios_id', '=', 'socios.id')
            ->where('estatus', '=', 'AUTORIZADO')
            ->get(['retiros.*', 'socios.nombre_completo']);

        return view('retiros.index', compact('retiro'));
    }

    public function create()
    {
        $retiro = new Retiro;

        return view('retiros.create', compact('retiro'));
    }

    public function store(Request $request)
    {
        $retiro = new Retiro();
        $socio = Socios::findorfail($request->input('socios_id'));
        $saldoAnteriro = $socio->saldo;
        $saldoActual = $socio->saldo - $request->input('monto_retiro');

        $names  = array(
            'socios_id' => 'SOCIOS',
            'monto_retiro' => 'MONTO A RETIRAR',
            'forma_pago' => 'FORMA DE PAGO',
        );

        $validator = Validator::make($request->all(), [
            'socios_id' => 'required|numeric|gt:0',
            'monto_retiro' => 'required|numeric|gt:0',
            'forma_pago' => ['required', 'string'],
        ], [], $names);

        //Validación personalizada para comparar con el saldo
        $validator->after(function ($validator) use ($request, $socio) {
            $montoRetiro = floatval($request->input('monto_retiro'));
            $saldo = floatval($socio->saldo);

            if ($montoRetiro > $saldo) {
                $validator->errors()->add('monto_retiro', 'El monto a retirar no puede ser mayor al saldo disponible.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
           // \DB::beginTransaction();

            $retiro->socios_id = $request->input('socios_id');
            $retiro->fecha_captura = Carbon::now();
            $retiro->monto_retiro = $request->input('monto_retiro');
            $retiro->saldo_aprobado = 0;
            $retiro->forma_pago = $request->input('forma_pago');
            $retiro->comentarios = '-';
            $retiro->estatus = 'PRE-AUTORIZADO';
            $retiro->save();

            /*
            // RETIRAMOS EL MONTO DE LA TABLA SOCIOS
            $socio->update([
                'saldo' => $socio->saldo - $request->input('monto_retiro'),
            ]);

            // INSERTAMOS EN LA TABLA MOVIMIENTOS
            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
            $nextId = $lastInsertedId + 1;

            Movimiento::create([
                'socios_id' => $retiro->socios_id,
                'fecha' => Carbon::now(),
                'folio' => 'MOV-' . $nextId,
                'saldo_anterior' => $saldoAnteriro,
                'saldo_actual' => $saldoActual,
                'monto' => $retiro->monto_retiro,
                'movimiento' => 'RETIRO',
                'tipo_movimiento' => 'CARGO',
                'metodo_pago' => $retiro->forma_pago,
                'estatus' => 'PRE-AUTORIZADO',
            ]);
            */

           // \DB::commit();

            return redirect()->route('admin.tesoreria.retiro.index')->with(['id' => $retiro->id]);
        } catch (\Exception $e) {
            //\DB::rollback();
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show(Retiro $retiro)
    {
        //
    }

    public function edit(Retiro $retiro)
    {
        //
    }

    public function update(Request $request, Retiro $retiro)
    {
        //
    }

    public function destroy(Retiro $retiro)
    {
        //
    }

    public function allSocios(Request $request)
    {
        /*$socios = Socios::where('activo', 1)
            ->whereRaw(' saldo - monto_prestamos > 0')
            ->orderBy('nombre_completo', 'asc')
            ->get();
 
        return json_encode($socios);*/

        $search = $request->input('search');

        $socios = Socios::where('activo', 1)
        //->whereRaw(' saldo - monto_prestamos > 0')
        ->whereRaw('COALESCE(saldo, 0) - COALESCE(monto_prestamos, 0) > 0') // Si hay valores nulos, podría ser problemático. En ese caso, podrías utilizar una función de SQL como COALESCE para evitar errores
        ->when($search, function($query, $search) {
            return $query->where('nombre_completo', 'like', "%{$search}%");
        })
        ->orderBy('nombre_completo', 'asc')
        ->limit(10)
        ->get();

        return response()->json($socios);
    }

    public function solicitudRetiro($id)
    {
        
        $retiro = Retiro::select(
            'retiros.*',
            'socios.num_socio',
            'socios.nombre_completo',
            'socios.domicilio',
            'socios.saldo',
            'socios.lugar_origen'
        )
        ->join('socios','retiros.socios_id','=','socios.id')
        ->where('retiros.id','=',$id)
        ->first();

        $socio = Socios::findorfail($retiro->socios_id);

        $prestamosDetalles = $socio->prestamoDetalles()
        ->where('debe', '>', 0)
        ->sum('debe');

        // OBTEGO EL SALDO PENDIENTE DE LOS PRESTAMOS
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

        $totalCapitalPendiente = $prestamos->sum('capital_pendiente');


        //  - CREAMOS EL PDF ----
        $pdf = PDF::loadView('recibos.solicitud_retiro', compact('retiro','prestamosDetalles','totalCapitalPendiente'))
            ->setPaper('letter', 'portrait');
        return $pdf->stream();
    }
}
