<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosTemporales extends Model
{
    use HasFactory;
    protected $dateFormat = 'Y-m-d';
    protected $table = "datos_temporales";
    protected $fillable = [
        'sector_id',
        'categoria_id',
        'num_socio',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'nombre_completo',
        'rfc',
        'fecha_alta',
        'telefono',
        'domicilio',
        'curp',
        'cuip',
        'estado_civil',
        'contacto_emergencia',
        'telefono_emergencia',
        'tipo_sangre',
        'lugar_origen',
        'alta_coorporacion',
        'compania',
        'batallon',
        'temporal_captura',
    ];
}
