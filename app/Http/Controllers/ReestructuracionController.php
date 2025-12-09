<?php

namespace App\Http\Controllers;

use App\Models\Socios;
use App\Models\Prestamos;
use App\Models\PrestamoDetalle;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

use Illuminate\Http\Request;

class ReestructuracionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:crear-prestamos-enfermedad', ['only'=>['index', 'store','show']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        // PENDIENTE ASOCIAR LA TABLA DE PAGOS PARA OBTENER LOS PRESTAMOS ACTIVO, PENDIENTES DE PAGO
        $socios = Socios::join('prestamos', 'socios.id', '=', 'prestamos.socios_id')
            ->where('estatus', '=', 'AUTORIZADO')
            ->select('socios.*')
            ->distinct()
            ->get();

        return view('reestructuracion.index', compact('socios'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            foreach ($request->prestamoId as $key => $value) {
                //echo $request->prestamoId[$key];

                // OBTENEMOS EL PRESTAMO
                $prestamo = Prestamos::findorfail($request->prestamoId[$key]);
                $id = $prestamo->id;
                $prestamoDetalle = PrestamoDetalle::where('prestamos_id', $request->prestamoId[$key])->get();
                $socio = Socios::where('id', $prestamo->socios_id)->get()->first();
                $name = $socio->nombre_completo;

                // FINALIZAMOS EL PRESTAMO
                $prestamo->update([
                    'estatus' => 'PAGADO',
                ]);

                // MOVIMIENTOS PARA EL SOCIO
                if ($socio !== null) { //!$socio->isEmpty()) {
                    $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                    $nextId = $lastInsertedId + 1;

                    $saldoAnteriro = ($socio->saldo  - $socio->monto_prestamos) + $socio->monto_prestamos;
                    $saldoActual = $saldoAnteriro + $prestamo->diferencia;

                    // ACTUALIZAMOS LOS SALDOS DEL SOCIO
                    $socio->update([
                        'monto_prestamos' => $socio->monto_prestamos - $prestamo->diferencia,
                        'numero_prestamos' => $socio->numero_prestamos - 1,
                    ]);

                    Movimiento::create([
                        'socios_id' => $socio->id,
                        'fecha' => Carbon::now(),
                        'folio' => 'MOV-' . $nextId,
                        'saldo_anterior' => $saldoAnteriro,
                        'saldo_actual' => $saldoAnteriro,
                        'monto' => $prestamo->diferencia,
                        'movimiento' => 'ABONO PRESTAMO. '. $prestamo->id, // AGREGAR UN CAMPO DE FOLIO
                        'tipo_movimiento' => 'ABONO',
                        'metodo_pago' => 'EFECTIVO',
                        'estatus' => 'AUTORIZADO',
                    ]);
                }

                // MOVIMIENTOS PARA EL AVAL
                if ($prestamoDetalle !== null) { //!$prestamoDetalle->isEmpty()) {
                    foreach ($prestamoDetalle as $row) {
                        $aval = Socios::where('id', $row->socios_id)->get()->first();

                        $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;
                        $nextId = $lastInsertedId + 1;

                        $saldoAnteriro = (($aval->saldo + 500) - $aval->monto_prestamos) + $aval->monto_prestamos;
                        $saldoActual = $saldoAnteriro + $row->monto_aval;

                        // ACTUALIZAMOS LOS SALDOS DEL AVAL
                        if ($row->apoyo_adicional == 0) {
                            $aval->update([
                                'monto_prestamos' => $aval->monto_prestamos - $row->monto_aval,
                                'is_aval' => $aval->is_aval - 1,
                            ]);
                        }

                        Movimiento::create([
                            'socios_id' => $aval->id,
                            'fecha' => Carbon::now(),
                            'folio' => 'MOV-' . $nextId,
                            'saldo_anterior' => $saldoAnteriro,
                            'saldo_actual' => $saldoAnteriro,
                            'monto' => $row->monto_aval,
                            'movimiento' => 'ABONO PRESTAMO. '. $prestamo->id,
                            'tipo_movimiento' => 'ABONO',
                            'metodo_pago' => 'EFECTIVO',
                            'estatus' => 'AUTORIZADO',
                        ]);
                    }
                }
            }
            \DB::commit();
            return redirect()->route('admin.reestructuracion.index')->with(['correcto' => 'success', 'name' => $name, 'id' => $id  ]);
            //dd('si lo envio');
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
        $socio = Socios::findorfail($id);
        $prestamos = $socio->prestamos;
        //OBTENGO LOS DATOS DEL USUARIO, PERO PARA OBTENER LOS DATOS DEL SOCIO
        // Obtén el usuario autenticado actual
        $user = Auth::user();
        $nombre = $user->name;
        $usuario = Socios::where('nombre_completo', '=', $nombre)
            ->select('socios.id', 'socios.nombre_completo')
            ->get()
            ->first();

        return view('reestructuracion.show', compact('prestamos', 'socio', 'usuario'));
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

    public function reciboRestructuracionPrestamoPagado($id)
    {
        $prestamo = Prestamos::findorfail($id);
        $socio = Socios::findorfail($prestamo->socios_id);
        $prestamoDetalle = PrestamoDetalle::join('socios', 'socios.id', '=', 'prestamo_detalles.socios_id')
            ->where('prestamos_id', $prestamo->id)
            ->select('prestamo_detalles.*', 'socios.nombre', 'socios.apellido_paterno', 'socios.apellido_materno', 'socios.rfc', 'socios.saldo', 'socios.monto_prestamos')
            ->get();

        //  - CREAMOS EL PDF ----
        $pdf = PDF::loadView('recibos.recibo_reestructuracion_prestamo_pagado', compact('prestamo', 'socio', 'prestamoDetalle'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream();
    }
}
