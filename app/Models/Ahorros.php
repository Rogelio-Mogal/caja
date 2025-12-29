<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Ahorros extends Model
{
    use HasFactory;
    protected $table = "ahorros";
    protected $fillable = [
        'socios_id',
        'fecha_ahorro',
        'monto',
        'metodo_pago',
        'referencia',
        'is_aportacion',
        'motivo_cancelacion',
        'activo'
    ];

    public function socio()
    {
        return $this->belongsTo(Socios::class, 'socios_id');
    }

    public function movimientos(): MorphMany
    {
        return $this->morphMany(Movimiento::class, 'origen');
    }
}
