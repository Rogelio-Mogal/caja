<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    use HasFactory;
    protected $table = "beneficiarios";
    protected $fillable = [
        'socios_id',
        'nombre_beneficiario',
        'domicilio_beneficiario',
        'telefono_beneficiario',
        'porcentaje_beneficiario',
        'activo',
    ];
    
    // RELACIONES

    // SOCIOS - BENEFICIARIOS
    public function socio()
    {
        // Define la relación "belongsTo" hacia Socios
        return $this->belongsTo(Socios::class, 'id');
    }
}
