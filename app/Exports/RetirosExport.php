<?php

namespace App\Exports;

use App\Models\Retiro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RetirosExport implements FromCollection, WithHeadings, WithMapping, WithEvents
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
        $this->datos = Retiro::with('socio')
        ->whereBetween('fecha_retiro', [$this->fechaInicio, $this->fechaFin])
        ->where('estatus', 'AUTORIZADO')
        ->get();

        return $this->datos;
    }

    public function headings(): array
    {
        return ['Fecha','Socio', 'Método de pago', 'Monto'];
    }

    public function map($retiro): array
    {
        return [
            $retiro->fecha_retiro,
            optional($retiro->socio)->nombre_completo,
            $retiro->forma_pago,
            $retiro->saldo_aprobado,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = count($this->datos) + 2; // +1 header, +1 start next row

                // Totales generales
                $totalMonto = $this->datos->sum('saldo_aprobado');
                $totalIntereses = $this->datos->sum('total_intereses');

                $event->sheet->setCellValue('A' . $rowCount, 'TOTAL GENERAL:');
                $event->sheet->setCellValue('D' . $rowCount, $totalMonto);

                // Totales por método de pago
                $rowCount += 2;
                $event->sheet->setCellValue('A' . $rowCount, 'TOTALES POR MÉTODO DE PAGO');

                $rowCount++;

                $event->sheet->setCellValue('A' . $rowCount, 'Método de pago');
                $event->sheet->setCellValue('B' . $rowCount, 'Total monto');

                $totalesPorMetodo = $this->datos->groupBy('forma_pago')->map(function ($grupo) {
                    return [
                        'total_monto' => $grupo->sum('saldo_aprobado'),
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
