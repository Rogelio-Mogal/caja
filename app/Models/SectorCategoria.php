<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorCategoria extends Model
{
    use HasFactory;

    protected $table = "sector_categorias";
    protected $fillable = [
        'tipo',
        'nombre',
        'wci',
        'activo',
    ];
}
