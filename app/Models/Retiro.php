<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retiro extends Model
{
    use HasFactory;
    protected $table = "retiros";
    protected $fillable = [
        'socios_id',
        'fecha_captura',
        'fecha_retiro',
        'monto_retiro',
        'saldo_aprobado',
        'saldo_actual',
        'forma_pago',
        'comentarios',
        'estatus',
    ];

    public function socio()
    {
        return $this->belongsTo(Socios::class, 'socios_id');
    }
}
