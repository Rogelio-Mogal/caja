<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfectivoDiario extends Model
{
    use HasFactory;
    protected $table = "efectivo_diarios";
    protected $fillable = [
        'fecha',
        'b_mil',
        'b_quinientos',
        'b_doscientos',
        'b_cien',
        'b_cincuenta',
        'b_veinte',
        'monedas',
        'total',
        'wci',
        'activo',
    ];
}
