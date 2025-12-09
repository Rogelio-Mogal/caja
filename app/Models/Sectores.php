<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sectores extends Model
{
    use HasFactory;
    protected $table = "sectores";
    protected $fillable = [
        'sector',
        'activo',
    ];

    // RELACION CON LA TABLA SOCIOS
    public function socios()
    {
        return $this->hasMany(Socios::class, 'sector_id');
    }
}
