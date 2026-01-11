<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagosPrestamosDetalles extends Model
{
    use HasFactory;
    protected $table = "pagos_prestamos_detalles";
    protected $fillable = [
        'pagos_prestamos_id',
        'prestamos_id',
        'socios_id',
        'tipo_cliente',
        'abona',

        'es_adelantado', // para saber si fue una liquidación y poder revertirlo
        'es_reversion', // para saber si fue una liquidación y poder revertirlo
        'reversion_de', // para saber si fue una liquidación y poder revertirlo

        'wci',
        'activo',
    ];
}
