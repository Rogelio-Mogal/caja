<?php

namespace App\Exports;

use App\Models\Movimiento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AhorrosExport implements FromCollection, WithHeadings, WithMapping, WithEvents
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
        $this->datos = Movimiento::with('socio')
            ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
            ->where('tipo_movimiento', 'ABONO')
            ->where('estatus', 'EFECTUADO')
            ->get();

        return $this->datos;
    }

    public function headings(): array
    {
        return ['Fecha','Socio' ,'Método de pago', 'Monto' ];
    }

    public function map($ahorro): array
    {
        return [
            $ahorro->fecha,
            optional($ahorro->socio)->nombre_completo,
            $ahorro->metodo_pago,
            $ahorro->monto,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = count($this->datos) + 2; // +1 header, +1 start next row

                // Totales generales
                $totalMonto = $this->datos->sum('monto');

                $event->sheet->setCellValue('A' . $rowCount, 'TOTAL GENERAL:');
                $event->sheet->setCellValue('D' . $rowCount, $totalMonto);

                // Totales por método de pago
                $rowCount += 2;
                $event->sheet->setCellValue('A' . $rowCount, 'TOTALES POR MÉTODO DE PAGO');

                $rowCount++;

                $event->sheet->setCellValue('A' . $rowCount, 'Método de pago');
                $event->sheet->setCellValue('B' . $rowCount, 'Total monto');

                $totalesPorMetodo = $this->datos->groupBy('metodo_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('monto'),
                    ];
                });

                foreach ($totalesPorMetodo as $metodo => $totales) {
                    $rowCount++;
                    $event->sheet->setCellValue('A' . $rowCount, $metodo);
                    $event->sheet->setCellValue('B' . $rowCount, $totales['total_monto']);
                }
                $totalGeneralMontoPorMetodo = $totalesPorMetodo->sum('total_monto');

                $rowCount++; // Avanza una fila

                $event->sheet->setCellValue('A' . $rowCount, 'Total General:');
                $event->sheet->setCellValue('B' . $rowCount, $totalGeneralMontoPorMetodo);
            }
        ];
    }
}
