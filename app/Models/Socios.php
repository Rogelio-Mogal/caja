<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Socios extends Model
{
    use HasFactory;
    protected $table = "socios";
    protected $fillable = [
        'sector_id',
        'categoria_id',
        'num_socio',
        'photo_path',
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
        'saldo',
        'monto_prestamos',
        'debia',
        'abona',
        'debe',
        'inscripcion',
        'numero_prestamos',
        'is_aval',
        'quincenas_inscrito',
        'temporal_captura',
        'tipo_usuario',
        'is_fundador',
        'users_id',
        'tipo',
        'fecha_baja',
        'observaciones',
        'activo',
    ];

    // RELACIONES

    // RELACION CON LA TABLA CATEGORIAS
    public function categoria()
    {
        //return $this->belongsTo(Categoria::class, 'categoria_id');
        return $this->belongsTo(SectorCategoria::class, 'categoria_id')->where('tipo', 'CATEGORÍA');
    }

    // RELACION CON LA TABLA SECTORES
    public function sector()
    {
        //return $this->belongsTo(Sectores::class, 'sector_id');
        return $this->belongsTo(SectorCategoria::class, 'sector_id')->where('tipo', 'SECTOR');
    }

    // SOCIOS - BENEFICIARIOS
    public function beneficiarios()
    {
        // Si un Socio puede tener múltiples Beneficiarios
        return $this->hasMany(Beneficiario::class);

        // Si un Socio puede tener solo un Beneficiario
        // return $this->hasOne(Beneficiario::class);
    }

    // SOCIOS - PRESTAMOS
    public function prestamos()
    {
        return $this->hasMany(Prestamos::class, 'socios_id');
    }

    // SOCIOS (AVALES) - PRESTAMOS
    public function prestamoDetalles()
    {
        return $this->hasMany(PrestamoDetalle::class, 'socios_id');
    }

    // para ontener los prestamos que hay un aval
    public function prestamosAvalados()
    {
        return $this->hasMany(PrestamoDetalle::class, 'socios_id');
    }

    // RELACIÓN CON DEVOLUCIÓN EFECTIVO
    public function devolucionesEfectivo()
    {
        return $this->hasMany(DevolucionEfectivo::class, 'socios_id');
    }

    // SOCIOS - AJUSTES
    public function ajustes()
    {
        return $this->hasMany(SociosAjustes::class, 'socios_id');
    }

    public function ahorros()
    {
        return $this->hasMany(Ahorros::class, 'socios_id');
    }

    public function movimientos(): MorphMany
    {
        return $this->morphMany(Movimiento::class, 'origen');
    }
}
