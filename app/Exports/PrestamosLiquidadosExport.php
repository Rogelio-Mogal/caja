<?php

namespace App\Exports;

use App\Models\PagosPrestamos;
use App\Models\Prestamos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PrestamosLiquidadosExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $datos;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        $this->datos = Prestamos::leftJoin('pagos_prestamos', function ($join) {
                $join->on('prestamos.id', '=', 'pagos_prestamos.prestamos_id')
                    ->where('pagos_prestamos.pagado', '=', 1); // Solo pagos NO pagados
            })
            // ->where('prestamos.socios_id', $id)
            ->whereBetween('prestamos.fecha_pago_reestructuracion', [$this->fechaInicio, $this->fechaFin])
            //->whereDate('prestamos.fecha_pago_reestructuracion', '=', Carbon::today())
            ->where('prestamos.estatus', 'AUTORIZADO')
            ->where('prestamos.prestamo_especial', 0)
            //->groupBy('prestamos.id', 'prestamos.monto_prestamo', 'prestamos.debe', 'prestamos.fecha_prestamo')
            ->groupBy(
                'prestamos.id',
                'prestamos.socios_id',
                'prestamos.fecha_pago_reestructuracion',
                'prestamos.fecha_prestamo',
                'prestamos.monto_prestamo',
                'prestamos.debe'
            )
            ->orderBy('prestamos.fecha_prestamo', 'asc')
            ->selectRaw('
                prestamos.id,
                prestamos.socios_id,
                prestamos.fecha_pago_reestructuracion,
                prestamos.fecha_prestamo,
                prestamos.monto_prestamo,
                prestamos.debe,
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
        return $this->datos;
    }

    public function headings(): array
    {
        return ['Fecha','Socio' ,'Método de pago', 'Monto préstamo', 'Monto pagado', 'Monto liquidado', 'Total'];
    }

    public function map($pagos): array
    {
        return [
            $pagos->fecha_pago_reestructuracion,
            optional($pagos->socio)->nombre_completo,
            $pagos->tipo_forma_pago,
            $pagos->monto_prestamo,
            $pagos->capital_sin_forma_pago,
            $pagos->capital_con_forma_pago,
            $pagos->capital_sin_forma_pago + $pagos->capital_con_forma_pago,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = count($this->datos) + 2; // +1 header, +1 start next row

                // Totales generales
                $totalMonto = $this->datos->sum('monto_prestamo');
                $totalIntereses = $this->datos->sum('capital_sin_forma_pago');
                $totalTres = $this->datos->sum('capital_con_forma_pago');
                $totalCuatro = $totalIntereses + $totalTres;

                $event->sheet->setCellValue('C' . $rowCount, 'TOTAL GENERAL:');
                $event->sheet->setCellValue('D' . $rowCount, $totalMonto);
                $event->sheet->setCellValue('E' . $rowCount, $totalIntereses);
                $event->sheet->setCellValue('F' . $rowCount, $totalTres);
                $event->sheet->setCellValue('G' . $rowCount, $totalCuatro);

                /*
                // Totales por método de pago
                $rowCount += 2;
                $event->sheet->setCellValue('A' . $rowCount, 'TOTALES POR MÉTODO DE PAGO');

                $rowCount++;

                $event->sheet->setCellValue('A' . $rowCount, 'Método de pago');
                $event->sheet->setCellValue('B' . $rowCount, 'Total monto');
                $event->sheet->setCellValue('C' . $rowCount, 'Total intereses');
                $event->sheet->setCellValue('D' . $rowCount, 'Monto + intereses');

                $totalesPorMetodo = $this->datos->groupBy('metodo_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('monto_prestamo'),
                        'total_intereses' => $grupo->sum('total_intereses'),
                    ];
                });

                foreach ($totalesPorMetodo as $metodo => $totales) {
                    $rowCount++;
                    $event->sheet->setCellValue('A' . $rowCount, $metodo);
                    $event->sheet->setCellValue('B' . $rowCount, $totales['total_monto']);
                    $event->sheet->setCellValue('C' . $rowCount, $totales['total_intereses']);
                    $event->sheet->setCellValue('D' . $rowCount, ($totales['total_monto'] + $totales['total_intereses']) );
                }
                $totalGeneralMontoPorMetodo = $totalesPorMetodo->sum('total_monto');
                $totalGeneralInteresesPorMetodo = $totalesPorMetodo->sum('total_intereses');

                $rowCount++; // Avanza una fila

                $event->sheet->setCellValue('A' . $rowCount, 'Total General:');
                $event->sheet->setCellValue('B' . $rowCount, $totalGeneralMontoPorMetodo);
                $event->sheet->setCellValue('C' . $rowCount, $totalGeneralInteresesPorMetodo);
                $event->sheet->setCellValue('D' . $rowCount, ($totalGeneralMontoPorMetodo + $totalGeneralInteresesPorMetodo));
                */
            }
        ];
    }
}
