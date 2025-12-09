<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagosPrestamos extends Model
{
    use HasFactory;
    protected $table = "pagos_prestamos";
    protected $fillable = [
        'prestamos_id',
        'socios_id',
        'fecha_pago',
        'fecha_captura',
        'serie_pago',
        'serie_final',
        'importe',
        'forma_pago',
        'metodo_pago',
        'referencia',
        'capital',
        'interes',
        'decuento',
        'fecha_tabla',
        'pagado',
        'wci',
        'activo',
    ];

    public function socio()
    {
        return $this->belongsTo(Socios::class, 'socios_id');
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamos::class, 'prestamos_id');
    }

}
