<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamos extends Model
{
    use HasFactory;
    protected $table = "prestamos";
    protected $fillable = [
        'socios_id',
        'fecha_captura',
        'fecha_prestamo',
        'fecha_ultimo_pago',
        'fecha_ultimo_descuento',
        'monto_prestamo',
        'total_intereses',
        'pago_quincenal',
        'total_quincenas',
        'debia',
        'abona',
        'debe',
        'fecha_pago_reestructuracion',
        'monto_pago_reestructuracion',
        'serie',
        'saldo_capital',
        'saldo_interes',
        'saldo_total',
        'metodo_pago',
        'diferencia',
        'fecha_primer_pago',
        'proximo_pago',
        'folio',
        'num_nomina',
        'num_empleado',
        'fecha_primer_corte',
        'motivo_cancelacion',
        'estatus',
        'apoyo_adicional',
        'prestamo_especial',
        'prestamo_enfermedad',
        'nota',
        'compara_pago',
        'documentacion',
        'activo',
    ];

    protected $casts = [
        'documentacion' => 'array',
    ];

    // PRESTAMOS - SOCIOS
    public function socio()
    {
        return $this->belongsTo(Socios::class, 'socios_id');
    }

    // PRESTAMOS - SOCIOS (AVALES)
    public function detalles()
    {
        return $this->hasMany(PrestamoDetalle::class, 'prestamos_id');
    }

    // PRESTAMOS - PagosPrestamos
    public function pagos()
    {
        return $this->hasMany(PagosPrestamos::class, 'prestamos_id');
    }

    // PRESTAMOS - PagosPrestamos, último pago pendiente (pagado = 0)
    public function ultimoPagoPendiente()
    {
        return $this->hasOne(PagosPrestamos::class, 'prestamos_id')
            ->where('pagado', 0)
            ->orderByDesc('serie_pago');
    }

    // PRESTAMOS - PagosPrestamos, Última serie pagada (pagado = 1)
    public function ultimaSeriePagada()
    {
        return $this->hasOne(PagosPrestamos::class, 'prestamos_id')
            ->where('pagado', 1)
            ->orderByDesc('serie_pago');
    }
}
