<?php

namespace App\Exports;

use App\Models\Prestamos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PrestamosExport implements FromCollection, WithHeadings, WithMapping, WithEvents
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
        /*
        return Prestamos::with('socio')
            ->whereBetween('fecha_prestamo', [$this->fechaInicio, $this->fechaFin])
            ->where('estatus', 'AUTORIZADO')
            ->get(); // campos deseados
            */

        $this->datos = Prestamos::with('socio')
        ->whereBetween('fecha_prestamo', [$this->fechaInicio, $this->fechaFin])
        ->where('estatus', 'AUTORIZADO')
        ->get();

        return $this->datos;
    }

    public function headings(): array
    {
        return ['Fecha','Socio' ,'Método de pago', 'Monto', 'Intereses', 'Monto+Intereses' ];
    }

    public function map($prestamo): array
    {
        return [
            $prestamo->fecha_prestamo,
            optional($prestamo->socio)->nombre_completo,
            $prestamo->metodo_pago,
            $prestamo->monto_prestamo,
            $prestamo->total_intereses,
            $prestamo->monto_prestamo + $prestamo->total_intereses,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = count($this->datos) + 2; // +1 header, +1 start next row

                // Totales generales
                $totalMonto = $this->datos->sum('monto_prestamo');
                $totalIntereses = $this->datos->sum('total_intereses');

                $event->sheet->setCellValue('A' . $rowCount, 'TOTAL GENERAL:');
                $event->sheet->setCellValue('D' . $rowCount, $totalMonto);
                $event->sheet->setCellValue('E' . $rowCount, $totalIntereses);
                $event->sheet->setCellValue('F' . $rowCount, ($totalMonto + $totalIntereses));

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
            }
        ];
    }
}
