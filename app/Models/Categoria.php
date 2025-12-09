<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $table = "categorias";
    protected $fillable = [
        'categoria',
        'activo',
    ];

    //RELACIONES

    // RELACION CON LA TABLA SOCIOS
    public function socios()
    {
        return $this->hasMany(Socios::class, 'categoria_id');
    }
}
