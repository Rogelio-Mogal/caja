<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionEfectivo extends Model
{
    use HasFactory;

    protected $table = "devolucion_efectivos";
    protected $fillable = [
        'socios_id',
        'fecha_captura',
        'fecha_devolucion',
        'importe',
        'forma_pago',
        'referencia',
        'nota',
        'estatus',
        'wci',
        'activo',
    ];

    public function socio()
    {
        return $this->belongsTo(Socios::class, 'socios_id');
    }
}
