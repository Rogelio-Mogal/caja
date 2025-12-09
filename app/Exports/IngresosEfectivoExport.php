<?php

namespace App\Exports;

use App\Models\Ahorros;
use App\Models\PagosPrestamos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;

class IngresosEfectivoExport implements FromCollection, WithMapping, WithEvents
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
        // 1. Ahorros en efectivo (no aportación)
        $ahorros_efectivo = Ahorros::whereBetween('fecha_ahorro', [$this->fechaInicio, $this->fechaFin])
            ->where('metodo_pago', 'EFECTIVO')
            ->where('is_aportacion', 0)
            ->sum('monto');

        // 2. Total préstamo efectivo (forma_pago no vacío)
        $prestamos_efectivo = PagosPrestamos::whereBetween('fecha_pago', [$this->fechaInicio, $this->fechaFin])
            ->whereNotNull('forma_pago')
            ->where('forma_pago', '!=', '')
            ->sum('capital');

        // 3. Aportaciones sociales en efectivo
        $aportaciones_efectivo = Ahorros::whereBetween('fecha_ahorro', [$this->fechaInicio, $this->fechaFin])
            ->where('metodo_pago', 'EFECTIVO')
            ->where('is_aportacion', 1)
            ->sum('monto');
        
        $total_aportacion_efectivo = $ahorros_efectivo + $prestamos_efectivo + $aportaciones_efectivo;

        // Resultado
        $this->datos = collect();

        $this->datos->push([
            'ahorros_efectivo' => $ahorros_efectivo,
            'prestamos_efectivo' => $prestamos_efectivo,
            'aportaciones_efectivo' => $aportaciones_efectivo,
            'total_aportacion_efectivo' => $total_aportacion_efectivo,
        ]);

        return $this->datos;
    }

    public function map($nomina): array
    {
        return [
            $nomina['ahorros_efectivo'],
            $nomina['prestamos_efectivo'],
            $nomina['aportaciones_efectivo'],
            $nomina['total_aportacion_efectivo'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Encabezados centrados estilo <h4>
                $titleRows = [
                    'CAJA DE AHORRO DE LA POLICÍA PREVENTIVA DEL ESTADO DE OAXACA',
                    'DETERMINACIÓN DE INGRESOS EN EFECTIVO',
                    'PARA DEPÓSITO DE LA CAJA DE TESORERÍA',
                    'FECHA ' . $this->fechaInicio->format('d/m/Y') . ' - ' . $this->fechaFin->format('d/m/Y')
                ];

                $rowIndex = 1;
                foreach ($titleRows as $title) {
                    $sheet->mergeCells("A{$rowIndex}:D{$rowIndex}");
                    $sheet->setCellValue("A{$rowIndex}", $title);
                    $sheet->getStyle("A{$rowIndex}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("A{$rowIndex}")->getFont()->setBold(true)->setSize(12);
                    $rowIndex++;
                }

                // Tabla de totales (inicio desde fila 6)
                $startRow = 6;

                $labels = [
                    'TOTAL DE AHORROS EN EFECTIVO',
                    'TOTAL PAGO DE PRÉSTAMOS EN EFECTIVO',
                    'TOTAL DE APORTACIÓN SOCIAL EN EFECTIVO',
                    'SALDO ENTRADAS DE EFECTIVO'
                ];

                $keys = [
                    'ahorros_efectivo',
                    'prestamos_efectivo',
                    'aportaciones_efectivo',
                    'total_aportacion_efectivo'
                ];

                $data = $this->datos->first();

                for ($i = 0; $i < 4; $i++) {
                    $row = $startRow + $i;
                    $sheet->setCellValue("A{$row}", $labels[$i]);
                    $sheet->setCellValue("B{$row}", 'MONTO');
                    $sheet->setCellValue("C{$row}", '$ ' . number_format($data[$keys[$i]], 2));

                    $sheet->getStyle("A{$row}:C{$row}")->getFont()->setSize(11);
                    $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setVertical('center');
                }

                // Ajustes de ancho de columnas
                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Opcional: más espacio entre títulos y tabla
                $sheet->getRowDimension(5)->setRowHeight(15);
            }
        ];
    }

}
