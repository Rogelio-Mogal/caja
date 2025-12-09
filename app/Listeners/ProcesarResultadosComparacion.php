<?php

namespace App\Listeners;

use App\Events\RegistrosComparados;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class ProcesarResultadosComparacion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegistrosComparados $event)
    {
        // Recuperar los resultados desde la caché temporal
        $resultados = Cache::get('registros_comparados');

        if ($resultados) {
            // Guardar los resultados en la caché de Laravel
            Cache::put('resultados_comparacion', $resultados, 60); // Guardar por 1 minuto
            
            /*$noDB = $resultados['noDB'];
            $noFinanciero = $resultados['noFinanciero'];

            // Construir $resultadoFinal
            $resultadoFinal = [
                'noDB' => $noDB,
                'noFinanciero' => $noFinanciero,
            ];

            // Procesar los resultados y hacer lo que necesites con ellos*/
        }
    }
}
