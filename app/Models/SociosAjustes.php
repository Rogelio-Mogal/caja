<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SociosAjustes extends Model
{
    use HasFactory;

    protected $table = "socios_ajustes";
    protected $fillable = [
        'socios_id',
        'tipo',
        'fecha',
        'observaciones',
        'wci',
    ];

     public function socio()
    {
        return $this->belongsTo(Socios::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'wci');
    }
}
