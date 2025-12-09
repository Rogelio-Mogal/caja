<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamosConceptos extends Model
{
    use HasFactory;
    protected $table = "prestamos_conceptos";
    protected $fillable = [
        'concepto',
        'comentarios',
        'precio',
        'num_plazos',
        'num_piezas',
        'disponibles',
        'activo',
    ];
}
