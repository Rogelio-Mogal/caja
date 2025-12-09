<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;
    protected $table = "movimientos";
    protected $fillable = [
        'socios_id',
        'fecha',
        'folio',
        'saldo_anterior',
        'saldo_actual',
        'monto',
        'movimiento',
        'tipo_movimiento',
        'metodo_pago',
        'estatus',
        'activo',
    ];

    public function socio()
    {
        return $this->belongsTo(Socios::class, 'socios_id');
    }
}
