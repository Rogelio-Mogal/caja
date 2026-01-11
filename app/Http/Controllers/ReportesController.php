<?php

namespace App\Http\Controllers;

use App\Exports\AhorrosExport;
use App\Exports\ArqueoCajaExport;
use App\Exports\IngresosEfectivoExport;
use App\Exports\PrestamosCancelaNominaExport;
use App\Models\Ahorros;
use App\Models\Movimiento;
use App\Models\PagosPrestamos;
use App\Models\Prestamos;
use App\Models\Retiro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;

use App\Exports\PrestamosExport;
use App\Exports\PrestamosLiquidadosExport;
use App\Exports\RetirosExport;
use App\Models\EfectivoDiario;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        //$this->middleware('permission:aprobar-retiro', ['only'=>['index','create','store']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index(Request $request)
    {
        $tipo = $request->query('tipo');

        if (!in_array($tipo, [
            'prestamos',
            'pago-liquidacion',
            'retiros', 'ahorros',
            'prestamo-pago-nomina',
            'ingreso-efectivo',
            'arqueo-caja'
            ])) {
            return redirect()->route('admin.reportes.index', ['tipo' => 'prestamos']);
        }

        $fechaInicio = $request->query('fecha_inicio');
        $fechaFin = $request->query('fecha_fin');

        $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($fechaFin)->endOfDay();

        // Carga los datos solo si ambas fechas están presentes
        $datos = collect();

        if ($fechaInicio && $fechaFin) {
            switch ($tipo) {
                case 'prestamos':
                    $datos = Prestamos::whereBetween('fecha_prestamo', [$fechaInicio, $fechaFin])
                        ->where('estatus', 'AUTORIZADO')
                        ->get();
                break;

                case 'pago-liquidacion':
                   /* $datos = PagosPrestamos::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                        ->get();*/

                    $datos = Prestamos::leftJoin('pagos_prestamos', function ($join) {
                        $join->on('prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                            ->where('pagos_prestamos.pagado', '=', 1); // Solo pagos NO pagados
                    })
                   // ->where('prestamos.socios_id', $id)
                    ->whereBetween('prestamos.fecha_pago_reestructuracion', [$fechaInicio, $fechaFin])
                    //->whereDate('prestamos.fecha_pago_reestructuracion', '=', Carbon::today())
                    ->where('prestamos.estatus', 'AUTORIZADO')
                    ->where('prestamos.prestamo_especial', 0)
                   // ->groupBy('prestamos.id', 'prestamos.monto_prestamo', 'prestamos.debe', 'prestamos.fecha_prestamo')
                    ->groupBy(
                        'prestamos.id',
                        'prestamos.socios_id',
                        'prestamos.num_nomina',
                        'prestamos.num_empleado',
                        'prestamos.fecha_pago_reestructuracion',
                        'prestamos.fecha_prestamo',
                        'prestamos.monto_prestamo',
                        'prestamos.debe',
                        'prestamos.fecha_ultimo_descuento',
                        'prestamos.pago_quincenal'
                    )
                    ->orderBy('prestamos.fecha_prestamo', 'asc')
                    ->selectRaw('
                        prestamos.id,
                        prestamos.socios_id,
                        prestamos.num_nomina,
                        prestamos.num_empleado,
                        prestamos.fecha_pago_reestructuracion,
                        prestamos.fecha_prestamo,
                        prestamos.monto_prestamo,
                        prestamos.debe,
                        prestamos.fecha_ultimo_descuento,
                        prestamos.pago_quincenal,
                        -- Suma de capital donde forma_pago está vacío
                        COALESCE(SUM(CASE
                            WHEN (pagos_prestamos.forma_pago IS NULL OR pagos_prestamos.forma_pago = "")
                            THEN pagos_prestamos.decuento
                            ELSE 0
                        END), 0) as capital_sin_forma_pago,

                        -- Suma de capital donde forma_pago tiene algún valor
                        COALESCE(SUM(CASE
                            WHEN (pagos_prestamos.forma_pago IS NOT NULL AND pagos_prestamos.forma_pago != "")
                            THEN pagos_prestamos.capital
                            ELSE 0
                        END), 0) as capital_con_forma_pago,

                        MAX(CASE
                            WHEN (pagos_prestamos.forma_pago IS NOT NULL AND pagos_prestamos.forma_pago != "" )
                            THEN pagos_prestamos.forma_pago
                        END) as tipo_forma_pago
                    ')
                    ->get();
                    //dd($datos);


                break;

                case 'retiros':
                    $datos = Retiro::whereBetween('fecha_retiro', [$fechaInicio, $fechaFin])
                        ->where('estatus', 'AUTORIZADO')
                        ->get();
                break;

                case 'ahorros':
                    $datos = Movimiento::whereBetween('fecha', [$fechaInicio, $fechaFin])
                        ->where('tipo_movimiento', 'ABONO')
                        ->where('estatus', 'EFECTUADO')
                        ->get();
                break;

                case 'prestamo-pago-nomina':

                    //$fechaInicio = Carbon::parse($fechaInicio)->toDateString();
                    //$fechaFin = Carbon::parse($fechaFin)->toDateString();
                    // Subconsulta: préstamos completamente pagados y sin forma de pago
                    $prestamosValidos = DB::table('pagos_prestamos')
                        ->select('prestamos_id')
                        ->where('activo', 1)
                        ->groupBy('prestamos_id')
                        ->havingRaw('COUNT(*) = SUM(pagado)')
                        //->havingRaw('SUM(CASE WHEN forma_pago IS NOT NULL THEN 1 ELSE 0 END) = 0')
                        ->pluck('prestamos_id');

                    $datos = DB::table('pagos_prestamos as pp')
                        ->join('prestamos as p', 'pp.prestamos_id', '=', 'p.id')
                        ->selectRaw("
                            YEAR(pp.fecha_tabla) as anio,
                            MONTH(pp.fecha_tabla) as mes,
                            CASE
                                WHEN DAY(pp.fecha_tabla) <= 15 THEN 1
                                ELSE 2
                            END as quincena,
                            SUM(pp.capital) as total_capital,
                            SUM(pp.interes) as total_interes,
                            SUM(pp.capital + pp.interes) as total_capital_interes,
                            SUM(p.pago_quincenal - pp.decuento) as diferencia_intereses
                        ")
                        ->where('pp.activo', 1)
                        ->where(function ($query) {
                            $query->whereNull('pp.forma_pago')
                                ->orWhere('pp.forma_pago', '');
                        })
                        ->whereNull('pp.forma_pago') // solo sumamos pagos sin forma de pago
                        ->whereIn('pp.prestamos_id', $prestamosValidos)
                        ->whereBetween('pp.fecha_tabla', [$fechaInicio, $fechaFin])
                        ->groupBy('anio', 'mes', 'quincena')
                        ->orderBy('anio')
                        ->orderBy('mes')
                        ->orderBy('quincena')
                        ->get();

                        //dd($prestamosValidos, $datos);

                    /*
                    $pagosFiltrados = PagosPrestamos::with(['prestamo', 'socio'])
                    ->whereBetween('fecha_tabla', [$fechaInicio, $fechaFin])
                    ->orderBy('serie_pago', 'desc')
                    ->get()
                    ->groupBy('prestamos_id');

                    // Resultado final
                    $datos = collect();

                    foreach ($pagosFiltrados as $prestamoId => $pagos) {
                        $ultimoPago = $pagos->first(); // ya está ordenado por serie desc

                        $datos->push([
                            'prestamos_id' => $prestamoId,
                            'completo' => $ultimoPago->pagado == 1,
                            'nombre_completo' => $ultimoPago->socio->nombre_completo ?? '',
                            'num_nomina' => $ultimoPago->prestamo->num_nomina ?? '',
                            'num_empleado' => $ultimoPago->prestamo->num_empleado ?? '',
                            'descuento' => $ultimoPago->decuento, // (asegúrate de que el campo en DB sea 'decuento')
                            'fecha_tabla' => \Carbon\Carbon::parse($ultimoPago->fecha_tabla)->format('d/m/y'),
                        ]);
                    }
                    */
                break;

                case 'ingreso-efectivo':

                        // 1. Ahorros en efectivo (no aportación)
                        $ahorros_efectivo = Ahorros::whereBetween('fecha_ahorro', [$fechaInicio, $fechaFin])
                            ->where('metodo_pago', 'EFECTIVO')
                            ->where('is_aportacion', 0)
                            ->sum('monto');

                        // 2. Total préstamo efectivo (forma_pago no vacío)
                        $prestamos_efectivo = PagosPrestamos::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                            ->whereNotNull('forma_pago')
                            ->where('forma_pago', '!=', '')
                            ->sum('capital');

                        // 3. Aportaciones sociales en efectivo
                        $aportaciones_efectivo = Ahorros::whereBetween('fecha_ahorro', [$fechaInicio, $fechaFin])
                            ->where('metodo_pago', 'EFECTIVO')
                            ->where('is_aportacion', 1)
                            ->sum('monto');

                        $total_aportacion_efectivo = $ahorros_efectivo + $prestamos_efectivo + $aportaciones_efectivo;

                        // Resultado
                        $datos = collect();

                        $datos->push([
                            'ahorros_efectivo' => $ahorros_efectivo,
                            'prestamos_efectivo' => $prestamos_efectivo,
                            'aportaciones_efectivo' => $aportaciones_efectivo,
                            'total_aportacion_efectivo' => $total_aportacion_efectivo,
                        ]);

                        //dd($datos);
                break;

                case 'arqueo-caja':

                        $fechaAnterior = Carbon::parse($fechaInicio)->subDay()->toDateString(); // "2025-07-07"

                        $saldo_inicial = EfectivoDiario::selectRaw('
                            SUM(b_mil) as b_mil,
                            SUM(b_quinientos) as b_quinientos,
                            SUM(b_doscientos) as b_doscientos,
                            SUM(b_cien) as b_cien,
                            SUM(b_cincuenta) as b_cincuenta,
                            SUM(b_veinte) as b_veinte,
                            SUM(monedas) as monedas,
                            SUM(total) as total
                        ')
                        ->whereDate('fecha', $fechaAnterior) // compara solo la fecha, ignora hora
                        ->where('activo', 1)
                        ->first();

                        $prestamos = Prestamos::whereBetween('fecha_prestamo', [$fechaInicio, $fechaFin])
                            ->where('metodo_pago', 'EFECTIVO')
                            ->where('activo', 1)
                            ->get();

                        $retiros =  Retiro::whereBetween('fecha_retiro', [$fechaInicio, $fechaFin])
                        ->where('estatus', 'AUTORIZADO')
                        ->where('forma_pago','EFECTIVO')
                        ->get();

                        $efectivo_suma = EfectivoDiario::selectRaw('
                            SUM(b_mil) as b_mil,
                            SUM(b_quinientos) as b_quinientos,
                            SUM(b_doscientos) as b_doscientos,
                            SUM(b_cien) as b_cien,
                            SUM(b_cincuenta) as b_cincuenta,
                            SUM(b_veinte) as b_veinte,
                            SUM(monedas) as monedas,
                            SUM(total) as total
                        ')
                        ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                        ->where('activo', 1)
                        ->first();


                        // Resultado
                        $datos = collect();

                        $datos->push([
                            'saldo_inicial' => $saldo_inicial,
                            'prestamos' => $prestamos,
                            'retiros' => $retiros,
                            'efectivo_diario' => $efectivo_suma,
                        ]);

                        //dd($datos);
                break;
            }

            // cálculo de totales para préstamos
            $totalMonto = 0;
            $totalIntereses = 0;
            $totalTres = 0;
            $totalCuatro = 0;
            $totalesPorMetodo = [];
            if ($tipo === 'prestamos') {
                $totalMonto = $datos->sum('monto_prestamo');
                $totalIntereses = $datos->sum('total_intereses');

                $totalesPorMetodo = $datos->groupBy('metodo_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('monto_prestamo'),
                        'total_intereses' => $grupo->sum('total_intereses'),
                    ];
                });
            }
            if ($tipo === 'pago-liquidacion') {
                $totalDescuento = $datos->sum('pago_quincenal');
                $totalMonto = $datos->sum('monto_prestamo');
                $totalIntereses = $datos->sum('capital_sin_forma_pago');
                $totalTres = $datos->sum('capital_con_forma_pago');
                $totalCuatro = $totalIntereses + $totalTres;

                $totalesPorMetodo = $datos->groupBy('metodo_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('monto_prestamo'),
                        'total_intereses' => $grupo->sum('capital_sin_forma_pago'),
                        'total_tres' => $grupo->sum('capital_con_forma_pago'),
                        'total_cuatro' => $grupo->sum('capital_sin_forma_pago') + $grupo->sum('capital_con_forma_pago'),
                    ];
                });
            }
            if ($tipo === 'retiros') {
                $totalMonto = $datos->sum('saldo_aprobado');

                $totalesPorMetodo = $datos->groupBy('forma_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('saldo_aprobado'),
                    ];
                });
            }
            if ($tipo === 'ahorros') {
                $totalMonto = $datos->sum('monto');

                $totalesPorMetodo = $datos->groupBy('metodo_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('monto'),
                    ];
                });
            }
        }

        // define valores por defecto si no son préstamos
        $totalMonto = $totalMonto ?? 0;
        $totalIntereses = $totalIntereses ?? 0;
        $totalTres = $totalTres ?? 0;
        $totalCuatro = $totalCuatro ?? 0;
        $totalesPorMetodo = $totalesPorMetodo ?? collect();
        $totalDescuento = $totalDescuento ?? 0;

        //dd($datos);

        return view('reportes.index', compact('tipo', 'datos', 'fechaInicio', 'fechaFin',
        'totalMonto', 'totalIntereses', 'totalesPorMetodo','totalTres', 'totalCuatro','totalDescuento')
        );
    }

    public function exportPrestamos(Request $request)
    {
        $tipo = $request->query('tipo');
        $fechaInicio = Carbon::parse($request->query('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->query('fecha_fin'))->endOfDay();

        if ($tipo == 'prestamos') {
            if ($request->query('formato') == 'excel') {
                return Excel::download(new PrestamosExport($fechaInicio, $fechaFin), 'reporte_prestamos.xlsx');
            }

            if ($request->query('formato') == 'pdf') {
                $datos = Prestamos::whereBetween('fecha_prestamo', [$fechaInicio, $fechaFin])
                    ->where('estatus', 'AUTORIZADO')
                    ->get();
                    // cálculo de totales para préstamos
                    $totalMonto = 0;
                    $totalIntereses = 0;
                    $totalesPorMetodo = [];

                    $totalMonto = $datos->sum('monto_prestamo');
                    $totalIntereses = $datos->sum('total_intereses');

                    $totalesPorMetodo = $datos->groupBy('metodo_pago')->map(function ($grupo) {
                        return [
                            'total_monto' => $grupo->sum('monto_prestamo'),
                            'total_intereses' => $grupo->sum('total_intereses'),
                        ];
                    });


                    // define valores por defecto si no son préstamos
                    $totalMonto = $totalMonto ?? 0;
                    $totalIntereses = $totalIntereses ?? 0;
                    $totalesPorMetodo = $totalesPorMetodo ?? collect();

                $pdf = PDF::loadView('reportes.pdf.prestamos', compact('datos','totalMonto', 'totalIntereses', 'totalesPorMetodo','fechaInicio', 'fechaFin') );
                return $pdf->download('reporte_prestamos.pdf');
            }
        }

        abort(404);
    }

    public function exportLiquidosPrestamos(Request $request)
    {
        $tipo = $request->query('tipo');

        $fechaInicio = Carbon::parse($request->query('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->query('fecha_fin'))->endOfDay();

        if ($tipo == 'pago-liquidacion') {
            if ($request->query('formato') == 'excel') {
                return Excel::download(new PrestamosLiquidadosExport($fechaInicio, $fechaFin), 'reporte_liquidos_prestamos.xlsx');
            }

            if ($request->query('formato') == 'pdf') {

                $datos = Prestamos::leftJoin('pagos_prestamos', function ($join) {
                    $join->on('prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                        ->where('pagos_prestamos.pagado', '=', 1); // Solo pagos NO pagados
                })
                // ->where('prestamos.socios_id', $id)
                ->whereBetween('prestamos.fecha_pago_reestructuracion', [$fechaInicio, $fechaFin])
                //->whereDate('prestamos.fecha_pago_reestructuracion', '=', Carbon::today())
                ->where('prestamos.estatus', 'AUTORIZADO')
                ->where('prestamos.prestamo_especial', 0)
                //->groupBy('prestamos.id', 'prestamos.monto_prestamo', 'prestamos.debe', 'prestamos.fecha_prestamo')
                ->groupBy(
                    'prestamos.id',
                    'prestamos.socios_id',
                    'prestamos.num_nomina',
                    'prestamos.num_empleado',
                    'prestamos.fecha_pago_reestructuracion',
                    'prestamos.fecha_prestamo',
                    'prestamos.monto_prestamo',
                    'prestamos.debe',
                    'prestamos.fecha_ultimo_descuento',
                    'prestamos.pago_quincenal'
                )
                ->orderBy('prestamos.fecha_prestamo', 'asc')
                ->selectRaw('
                    prestamos.id,
                    prestamos.socios_id,
                    prestamos.num_nomina,
                    prestamos.num_empleado,
                    prestamos.fecha_pago_reestructuracion,
                    prestamos.fecha_prestamo,
                    prestamos.monto_prestamo,
                    prestamos.debe,
                    prestamos.fecha_ultimo_descuento,
                    prestamos.pago_quincenal,
                    -- Suma de capital donde forma_pago está vacío
                    COALESCE(SUM(CASE
                        WHEN (pagos_prestamos.forma_pago IS NULL OR pagos_prestamos.forma_pago = "")
                        THEN pagos_prestamos.decuento
                        ELSE 0
                    END), 0) as capital_sin_forma_pago,

                    -- Suma de capital donde forma_pago tiene algún valor
                    COALESCE(SUM(CASE
                        WHEN (pagos_prestamos.forma_pago IS NOT NULL AND pagos_prestamos.forma_pago != "")
                        THEN pagos_prestamos.capital
                        ELSE 0
                    END), 0) as capital_con_forma_pago,

                    MAX(CASE
                        WHEN (pagos_prestamos.forma_pago IS NOT NULL AND pagos_prestamos.forma_pago != "" )
                        THEN pagos_prestamos.forma_pago
                    END) as tipo_forma_pago
                ')
                ->get();

                $totalMonto = $datos->sum('monto_prestamo');
                $totalIntereses = $datos->sum('capital_sin_forma_pago');
                $totalTres = $datos->sum('capital_con_forma_pago');
                $totalCuatro = $totalIntereses + $totalTres;

                $pdf = PDF::loadView('reportes.pdf.liquidacion_prestamo', compact('datos','totalMonto', 'totalIntereses', 'totalTres', 'totalCuatro'));
                return $pdf->download('reporte_liquidos_prestamos.pdf');
            }
        }

        abort(404);
    }

    public function exportRetiros(Request $request)
    {
        $tipo = $request->query('tipo');
        $fechaInicio = Carbon::parse($request->query('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->query('fecha_fin'))->endOfDay();

        if ($tipo == 'retiros') {
            if ($request->query('formato') == 'excel') {
                return Excel::download(new RetirosExport($fechaInicio, $fechaFin), 'reporte_retiros.xlsx');
            }

            if ($request->query('formato') == 'pdf') {
                $datos = Retiro::whereBetween('fecha_retiro', [$fechaInicio, $fechaFin])
                    ->where('estatus', 'AUTORIZADO')
                    ->get();

                // cálculo de totales para préstamos
                $totalMonto = 0;
                $totalIntereses = 0;
                $totalesPorMetodo = [];

                $totalMonto = $datos->sum('saldo_aprobado');

                $totalesPorMetodo = $datos->groupBy('forma_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('saldo_aprobado'),
                    ];
                });


                // define valores por defecto si no son préstamos
                $totalMonto = $totalMonto ?? 0;
                $totalIntereses = $totalIntereses ?? 0;
                $totalesPorMetodo = $totalesPorMetodo ?? collect();

                $pdf = PDF::loadView('reportes.pdf.retiros', compact('datos','totalMonto', 'totalIntereses', 'totalesPorMetodo','fechaInicio', 'fechaFin'));
                return $pdf->download('reporte_retiros.pdf');
            }
        }

        abort(404);
    }

    public function exportAhorros(Request $request)
    {
        $tipo = $request->query('tipo');
        $fechaInicio = Carbon::parse($request->query('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->query('fecha_fin'))->endOfDay();

        if ($tipo == 'ahorros') {
            if ($request->query('formato') == 'excel') {
                return Excel::download(new AhorrosExport($fechaInicio, $fechaFin), 'reporte_ahorros.xlsx');
            }

            if ($request->query('formato') == 'pdf') {
                $datos = Movimiento::whereBetween('fecha', [$fechaInicio, $fechaFin])
                    ->where('tipo_movimiento', 'ABONO')
                    ->where('estatus', 'EFECTUADO')
                    ->get();

                // cálculo de totales para préstamos
                $totalMonto = 0;
                $totalIntereses = 0;
                $totalesPorMetodo = [];

                $totalMonto = $datos->sum('monto');

                $totalesPorMetodo = $datos->groupBy('metodo_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('monto'),
                    ];
                });


                // define valores por defecto si no son préstamos
                $totalMonto = $totalMonto ?? 0;
                $totalIntereses = $totalIntereses ?? 0;
                $totalesPorMetodo = $totalesPorMetodo ?? collect();

                $pdf = PDF::loadView('reportes.pdf.ahorros', compact('tipo', 'datos', 'fechaInicio', 'fechaFin',
                'totalMonto', 'totalIntereses', 'totalesPorMetodo'));
                return $pdf->download('reporte_ahorros.pdf');
            }
        }

        abort(404);
    }

    public function exportCancelaPrestamoNomina(Request $request)
    {
        $tipo = $request->query('tipo');
        $fechaInicio = Carbon::parse($request->query('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->query('fecha_fin'))->endOfDay();

        if ($tipo == 'prestamo-pago-nomina') {
            if ($request->query('formato') == 'excel') {
                return Excel::download(new PrestamosCancelaNominaExport($fechaInicio, $fechaFin), 'reporte_prestamo_cancela_nomina.xlsx');
            }

            if ($request->query('formato') == 'pdf') {

                $pagosFiltrados = PagosPrestamos::with(['prestamo', 'socio'])
                ->whereBetween('fecha_tabla', [$fechaInicio, $fechaFin])
                ->orderBy('serie_pago', 'desc')
                ->get()
                ->groupBy('prestamos_id');

                // Resultado final
                $datos = collect();

                foreach ($pagosFiltrados as $prestamoId => $pagos) {
                    $ultimoPago = $pagos->first(); // ya está ordenado por serie desc

                    $datos->push([
                        'prestamos_id' => $prestamoId,
                        'completo' => $ultimoPago->pagado == 1,
                        'nombre_completo' => $ultimoPago->socio->nombre_completo ?? '',
                        'num_nomina' => $ultimoPago->prestamo->num_nomina ?? '',
                        'num_empleado' => $ultimoPago->prestamo->num_empleado ?? '',
                        'descuento' => $ultimoPago->decuento, // (asegúrate de que el campo en DB sea 'decuento')
                        'fecha_tabla' => \Carbon\Carbon::parse($ultimoPago->fecha_tabla)->format('d/m/y'),
                    ]);
                }

                $pdf = PDF::loadView('reportes.pdf.prestamo_cancela_nomina', compact('tipo', 'datos', 'fechaInicio', 'fechaFin'));
                return $pdf->download('reporte_prestamo_cancela_nomina.pdf');
            }
        }

        abort(404);
    }

    public function exportIngresosEfectivo(Request $request)
    {
        $tipo = $request->query('tipo');
        $fechaInicio = Carbon::parse($request->query('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->query('fecha_fin'))->endOfDay();

        if ($tipo == 'ingreso-efectivo') {
            if ($request->query('formato') == 'excel') {
                return Excel::download(new IngresosEfectivoExport($fechaInicio, $fechaFin), 'reporte_ingreso_efectivo.xlsx');
            }

            if ($request->query('formato') == 'pdf') {

                // 1. Ahorros en efectivo (no aportación)
                $ahorros_efectivo = Ahorros::whereBetween('fecha_ahorro', [$fechaInicio, $fechaFin])
                    ->where('metodo_pago', 'EFECTIVO')
                    ->where('is_aportacion', 0)
                    ->sum('monto');

                // 2. Total préstamo efectivo (forma_pago no vacío)
                $prestamos_efectivo = PagosPrestamos::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                    ->whereNotNull('forma_pago')
                    ->where('forma_pago', '!=', '')
                    ->sum('capital');

                // 3. Aportaciones sociales en efectivo
                $aportaciones_efectivo = Ahorros::whereBetween('fecha_ahorro', [$fechaInicio, $fechaFin])
                    ->where('metodo_pago', 'EFECTIVO')
                    ->where('is_aportacion', 1)
                    ->sum('monto');

                $total_aportacion_efectivo = $ahorros_efectivo + $prestamos_efectivo + $aportaciones_efectivo;

                // Resultado
                $datos = collect();

                $datos->push([
                    'ahorros_efectivo' => $ahorros_efectivo,
                    'prestamos_efectivo' => $prestamos_efectivo,
                    'aportaciones_efectivo' => $aportaciones_efectivo,
                    'total_aportacion_efectivo' => $total_aportacion_efectivo,
                ]);


                $pdf = PDF::loadView('reportes.pdf.ingreso_efectivo', compact('tipo', 'datos', 'fechaInicio', 'fechaFin'));
                return $pdf->download('reporte_ingreso_efectivo.pdf');
            }
        }

        abort(404);
    }

    public function exportArqueoCaja(Request $request)
    {
        $tipo = $request->query('tipo');
        $fechaInicio = Carbon::parse($request->query('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->query('fecha_fin'))->endOfDay();


        if ($tipo == 'arqueo-caja') {
            if ($request->query('formato') == 'excel') {


                return Excel::download(new ArqueoCajaExport($fechaInicio, $fechaFin), 'reporte_arqueo_caja.xlsx');
            }

            if ($request->query('formato') == 'pdf') {

                $fechaAnterior = Carbon::parse($fechaInicio)->subDay()->toDateString(); // "2025-07-07"

                $saldo_inicial = EfectivoDiario::selectRaw('
                    SUM(b_mil) as b_mil,
                    SUM(b_quinientos) as b_quinientos,
                    SUM(b_doscientos) as b_doscientos,
                    SUM(b_cien) as b_cien,
                    SUM(b_cincuenta) as b_cincuenta,
                    SUM(b_veinte) as b_veinte,
                    SUM(monedas) as monedas,
                    SUM(total) as total
                ')
                ->whereDate('fecha', $fechaAnterior) // compara solo la fecha, ignora hora
                ->where('activo', 1)
                ->first();

                $prestamos = Prestamos::whereBetween('fecha_prestamo', [$fechaInicio, $fechaFin])
                    ->where('metodo_pago', 'EFECTIVO')
                    ->where('activo', 1)
                    ->get();

                $retiros =  Retiro::whereBetween('fecha_retiro', [$fechaInicio, $fechaFin])
                ->where('estatus', 'AUTORIZADO')
                ->where('forma_pago','EFECTIVO')
                ->get();

                $efectivo_suma = EfectivoDiario::selectRaw('
                    SUM(b_mil) as b_mil,
                    SUM(b_quinientos) as b_quinientos,
                    SUM(b_doscientos) as b_doscientos,
                    SUM(b_cien) as b_cien,
                    SUM(b_cincuenta) as b_cincuenta,
                    SUM(b_veinte) as b_veinte,
                    SUM(monedas) as monedas,
                    SUM(total) as total
                ')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->where('activo', 1)
                ->first();

                // Resultado
                $datos = collect();

                $datos->push([
                    'saldo_inicial' => $saldo_inicial,
                    'prestamos' => $prestamos,
                    'retiros' => $retiros,
                    'efectivo_diario' => $efectivo_suma,
                ]);


                $pdf = PDF::loadView('reportes.partials.arqueo_caja', compact('tipo', 'datos', 'fechaInicio', 'fechaFin'));
                return $pdf->download('reporte_arqueo_caja.pdf');
            }
        }

        abort(404);
    }
}
