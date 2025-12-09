<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ResultadoComparacionController extends Controller
{
    public function obtenerResultados()
    {
        $resultados = Cache::get('resultados_comparacion');

        return response()->json($resultados);
    }
}
