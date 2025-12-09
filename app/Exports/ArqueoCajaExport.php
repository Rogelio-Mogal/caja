<?php

namespace App\Exports;

use App\Models\EfectivoDiario;
use App\Models\Prestamos;
use App\Models\Retiro;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ArqueoCajaExport implements FromCollection, WithEvents
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
        $fechaAnterior = Carbon::parse($this->fechaInicio)->subDay()->toDateString(); // "2025-07-07"
                        
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

        $prestamos = Prestamos::whereBetween('fecha_prestamo', [$this->fechaInicio, $this->fechaFin])
            ->where('metodo_pago', 'EFECTIVO')
            ->where('activo', 1)
            ->get();

        $retiros =  Retiro::whereBetween('fecha_retiro', [$this->fechaInicio, $this->fechaFin])
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
        ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
        ->where('activo', 1)
        ->first();

        // Resultado
       /* $this->datos = collect();

        $this->datos->push([
            'saldo_inicial' => $saldo_inicial,
            'prestamos' => $prestamos,
            'retiros' => $retiros,
            'efectivo_diario' => $efectivo_suma,
        ]);

        return $this->datos;*/

        $this->datos = [
            'saldo_inicial' => $saldo_inicial,
            'prestamos' => $prestamos,
            'retiros' => $retiros,
            'efectivo_diario' => $efectivo_suma,
        ];

        return new Collection([]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                $data = $this->datos;
                $row = 1;

                // Títulos principales
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row", "REPORTE ARQUEO DE CAJA");
                $row += 2;

                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row", "TOTAL DE PRÉSTAMOS EN EFECTIVO");

                $sheet->mergeCells("D$row:E$row");
                $sheet->setCellValue("D$row", "DENOMINACIÓN TOTAL");

                $row++;

                // Saldo inicial
                $saldoInicial = $data['saldo_inicial']?->total ?? 0;
                $sheet->setCellValue("A$row", "SALDO INICIAL");
                $sheet->setCellValue("B$row", $saldoInicial);

                $den = $data['efectivo_diario'];
                $sheet->setCellValue("D$row", "{$den->b_mil} DE \$1,000");
                $sheet->setCellValue("E$row", $den->b_mil * 1000);
                $row++;

                // Préstamos
                $totalPrestamos = 0;
                foreach ($data['prestamos'] as $i => $p) {
                    $sheet->setCellValue("A$row", "PRÉSTAMO " . ($i+1));
                    $sheet->setCellValue("B$row", $p->diferencia);
                    $totalPrestamos += $p->diferencia;

                    $denVal = match ($i) {
                        0 => [$den->b_quinientos, 500],
                        1 => [$den->b_doscientos, 200],
                        2 => [$den->b_cien, 100],
                        3 => [$den->b_cincuenta, 50],
                        4 => [$den->b_veinte, 20],
                        default => [null, null],
                    };

                    if ($denVal[0] !== null) {
                        $sheet->setCellValue("D$row", "{$denVal[0]} DE \${$denVal[1]}");
                        $sheet->setCellValue("E$row", $denVal[0] * $denVal[1]);
                    }

                    $row++;
                }

                // Retiros
                $totalRetiros = 0;
                foreach ($data['retiros'] as $i => $r) {
                    $sheet->setCellValue("A$row", "RET DE AHORRO " . ($i+1));
                    $sheet->setCellValue("B$row", $r->saldo_aprobado);
                    $totalRetiros += $r->saldo_aprobado;
                    $row++;
                }

                // Totales
                $sheet->setCellValue("A$row", "TOTAL PRÉSTAMOS");
                $sheet->setCellValue("B$row", $totalPrestamos);
                $row++;

                $sheet->setCellValue("A$row", "TOTAL RETIROS");
                $sheet->setCellValue("B$row", $totalRetiros);
                $row++;

                $saldoFinal = $saldoInicial - $totalPrestamos - $totalRetiros;
                $sheet->setCellValue("A$row", "SALDO FINAL");
                $sheet->setCellValue("B$row", $saldoFinal);

                // MONEDAS
                $sheet->setCellValue("D$row", "MONEDAS");
                $sheet->setCellValue("E$row", $den->monedas);
                $row++;

                // Total denominación
                $sheet->setCellValue("D$row", "TOTAL");
                $sheet->setCellValue("E$row", $den->total);
                $sheet->getStyle("D$row:E$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFC000');
                $row++;

                // Diferencia
                $diferencia = $den->total - $saldoFinal;
                $sheet->setCellValue("D$row", "DIFERENCIA");
                $sheet->setCellValue("E$row", $diferencia);
                $sheet->getStyle("D$row:E$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFBFBFBF');
            }
        ];
    }
}
