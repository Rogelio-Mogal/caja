<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tesoreria extends Model
{
    use HasFactory;
    protected $table = "abonos";
    protected $fillable = [
        'forma_pago_id',
        'cliente_id',
        'debia',
        'abona',
        'debe',
        'wci',
    ];
}
