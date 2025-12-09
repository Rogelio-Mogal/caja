<?php

namespace App\Exports;

use App\Models\PagosPrestamos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PrestamosCancelaNominaExport implements FromCollection, WithHeadings, WithMapping
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
        /*$this->datos = Movimiento::with('socio')
            ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
            ->where('tipo_movimiento', 'ABONO')
            ->where('estatus', 'EFECTUADO')
            ->get();*/



        $this->datos = PagosPrestamos::with(['prestamo', 'socio'])
        ->whereBetween('fecha_tabla', [$this->fechaInicio, $this->fechaFin])
        ->orderBy('serie_pago', 'desc')
        ->get()
        ->groupBy('prestamos_id')
        ->map(function ($pagos, $prestamoId) {
            $ultimoPago = $pagos->first(); // Ya está ordenado por serie descendente

            return [
                'prestamos_id' => $prestamoId,
                'completo' => $ultimoPago->pagado == 1,
                'nombre_completo' => $ultimoPago->socio->nombre_completo ?? '',
                'num_nomina' => $ultimoPago->prestamo->num_nomina ?? '',
                'num_empleado' => $ultimoPago->prestamo->num_empleado ?? '',
                'descuento' => $ultimoPago->decuento,
                'fecha_tabla' => \Carbon\Carbon::parse($ultimoPago->fecha_tabla)->format('d/m/y'),
            ];
        })
        ->values(); // Para resetear los índices

        return $this->datos;
    }

    public function headings(): array
    {
        return ['N° NOMINA','N° EMPLEADO' ,'NOMBRE DEL SOCIO', 'DESCUENTO QUINCENAL', 'QUINCENA A LA CUAL SE LIQUIDA' ];
    }

    public function map($nomina): array
    {
        return [
            $nomina['num_nomina'],
            $nomina['num_empleado'],
            $nomina['nombre_completo'],
            $nomina['descuento'],
            $nomina['fecha_tabla'],
        ];
    }
}
