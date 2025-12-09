<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DatosTemporales;
use App\Models\Socios;
use Illuminate\Support\Facades\Cache;

class CompararRegistros implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        /*
        // Obtener registros de DatosTemporales y Socios
        $datosTemporales = DatosTemporales::pluck('nombre_completo');
        $socios = Socios::pluck('nombre_completo');

        // Encontrar registros que están en DatosTemporales pero no en Socios
        $noDB = $datosTemporales->diff($socios);

        // Encontrar registros que están en Socios pero no en DatosTemporales
        $noFinanciero = $socios->diff($datosTemporales);

        // Guardar los resultados en caché temporal
        Cache::put('registros_comparados', [
            'noDB' => $noDB,
            'noFinanciero' => $noFinanciero,
        ], 60); // Guardar por 1 minuto*/

        // Obtener registros de DatosTemporales y Socios
        // Obtener registros de DatosTemporales y Socios
        $datosTemporales = DatosTemporales::pluck('nombre_completo')->map(function ($nombre) {
            return trim($nombre);
        })->toArray();
        
        $socios = Socios::pluck('nombre_completo')->map(function ($nombre) {
            return trim($nombre);
        })->toArray();

        // Encontrar registros que están en DatosTemporales pero no en Socios
        $noDB = array_diff($datosTemporales, $socios);

        // Encontrar registros que están en Socios pero no en DatosTemporales
        $noFinanciero = array_diff($socios, $datosTemporales);

        // Actualizar los campos temporal_captura en DatosTemporales y Socios
        DatosTemporales::whereIn('nombre_completo', $noDB)->update(['temporal_captura' => 'No en DB']);
        Socios::whereIn('nombre_completo', $noFinanciero)->update(['temporal_captura' => 'No en Financiero']);


    }
}
