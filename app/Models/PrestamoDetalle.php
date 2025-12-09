<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamoDetalle extends Model
{
    use HasFactory;
    protected $table = "prestamo_detalles";
    protected $fillable = [
        'prestamos_id',
        'socios_id',
        'aval',
        'num_aval',
        'monto_socio',
        'monto_aval',
        'fecha_ultimo_pago',
        'debia',
        'abona',
        'debe',
        'fecha_pago_reestructuracion',
        'monto_pago_reestructuracion',
        'num_nomina',
        'num_empleado',
        'apoyo_adicional',
    ];

    // PRETAMOS DETALLE - SOCIOS (AVALES)
    public function socio()
    {
        return $this->belongsTo(Socios::class, 'socios_id');
    }

    // PRETAMOS DETALLE - PRESTAMOS (AVALES)
    public function prestamo()
    {
        return $this->belongsTo(Prestamos::class, 'prestamos_id');
    }
}
