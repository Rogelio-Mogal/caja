<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class HistorialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        // SOCIOS-HISTORIAL
        $this->middleware('permission:historial-prestamo', ['only'=>['index','show']]);

        /*$this->middleware('can:paciente.index')->only('index');
        $this->middleware('can:paciente.crear')->only('create','store');
        $this->middleware('can:paciente.editar')->only('edit','update');
        $this->middleware('can:paciente.eliminar')->only('destroy');*/
    }   

    public function index(Request $request)
    {
        $tipo = 'historial'; //$request->query('tipo');

        $fechaInicio = $request->query('fecha_inicio');
        $fechaFin = $request->query('fecha_fin');

        $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($fechaFin)->endOfDay();

        // Carga los datos solo si ambas fechas están presentes
        $datos = collect();

        if ($fechaInicio && $fechaFin) {
            switch ($tipo) {
                case 'historial':
                    $datos = Movimiento::join('socios', 'movimientos.socios_id', '=', 'socios.id')
                        ->select('movimientos.*', 'socios.nombre_completo')
                        ->where('movimientos.socios_id', $request->input('socios_id'))
                        ->whereBetween('movimientos.fecha', [$fechaInicio, $fechaFin])
                        ->get();
                    break;
            }
        }

        return view('historial.index', compact('tipo', 'datos', 'fechaInicio', 'fechaFin'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, $id)
    {
        $fechaInicio = Carbon::createFromFormat('d/m/Y', $request->input('f_inicia'))->startOfDay();
        $fechaTermino = Carbon::createFromFormat('d/m/Y', $request->input('f_termina'))->endOfDay();

        $historial = Movimiento::join('socios', 'movimientos.socios_id', '=', 'socios.id')
            ->select('movimientos.*', 'socios.nombre_completo')
            ->where('movimientos.socios_id', $request->input('socios_id'))
            ->whereBetween('movimientos.fecha', [$fechaInicio, $fechaTermino])
            ->get();

        return json_encode($historial);
    }

    public function exportHistorial(Request $request)
    {
        $tipo = 'historial';
        $fechaInicio = Carbon::parse($request->query('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->query('fecha_fin'))->endOfDay();

        if ($tipo == 'historial') {

            if ($request->query('formato') == 'pdf') {
                $datos = Movimiento::join('socios', 'movimientos.socios_id', '=', 'socios.id')
                    ->select('movimientos.*', 'socios.nombre_completo')
                    ->where('movimientos.socios_id', $request->input('socios_id'))
                    ->whereBetween('movimientos.fecha', [$fechaInicio, $fechaFin])
                    ->get();

                $pdf = PDF::loadView('historial.pdf.historial_pdf', compact('datos', 'fechaInicio', 'fechaFin') );
                return $pdf->download('reporte_historial.pdf');
            }
        }

        abort(404);
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
