<?php

namespace App\Http\Controllers;

use App\Models\ExcelAhorro;
use App\Models\Socios;
use App\Models\DatosTemporales;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class ExcelAhorroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:agregar-ahorro-excel', ['only'=>['create', 'store']]);

        /*$this->middleware('can:paciente.index')->only('index');
        $this->middleware('can:paciente.crear')->only('create','store');
        $this->middleware('can:paciente.editar')->only('edit','update');
        $this->middleware('can:paciente.eliminar')->only('destroy');*/
    }

    public function index()
    {
        //
    }

    public function create()
    {
        return view('excel_ahorro.create');
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            // COPIAMOS EL CONTENIDO DE LA TABLA datos_temporales A LA TABLA socios
            $registrosOrigen = Socios::whereNull('temporal_captura')
            ->orWhere('temporal_captura', '=', '')
            ->get();

            // Obtener el último ID insertado en la tabla de movimientos
            $lastInsertedId = Movimiento::orderBy('id', 'desc')->first()->id ?? 0;

            // Inserta los registros en la tabla movimientos
            foreach ($registrosOrigen as $registro) {
                $nextId = $lastInsertedId + 1;
                $saldoAnteriro = $registro->saldo;
                $saldoActual = $registro->saldo + 200;
                $movimiento = Movimiento::create([
                    'socios_id' => $registro->id,
                    'fecha' => Carbon::now(),
                    'folio' => 'MOV-' . $nextId,
                    'saldo_anterior' => $saldoAnteriro,
                    'saldo_actual' => $saldoActual,
                    'monto' => 200,
                    'movimiento' => 'INGRESO',
                    'tipo_movimiento' => 'ABONO',
                    'metodo_pago' => 'EFECTIVO',
                    'estatus' => 'EFECTUADO',
                ]);

                // Incrementar el último ID insertado
                $lastInsertedId = $nextId;

                // ACTUALIZAMOS EL MONTO DE LA TABLA socios
                $socio = Socios::where('id','=' , $movimiento->socios_id )
                ->get()
                ->first();

                if ($socio) {
                    $socio->update([
                        'saldo' => $socio->saldo + 200,
                    ]);
                } else {
                    // Manejar el caso donde no se encontró el socio
                    //throw new Exception("Socio con ID {$movimiento->socio_id} no encontrado.");
                }
            }

            \DB::commit();

            // Reseteamos el campo de temporal_captura de la tabla SOCIOS
            Socios::query()->update(['temporal_captura' => '']);

            // VACIAMOS LA TABLA datos_temporales
            DatosTemporales::truncate();

            return redirect()->route('admin.socios.index');
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error'=> 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput( $request->all(),$query );
        }
    }

    public function show(ExcelAhorro $excelAhorro)
    {
        //
    }

    public function edit(ExcelAhorro $excelAhorro)
    {
        //
    }

    public function update(Request $request, ExcelAhorro $excelAhorro)
    {
        //
    }

    public function destroy(ExcelAhorro $excelAhorro)
    {
        //
    }

    public function leerArchivoExcelAhorro(Request $request)
    {
        // -- ( 2 ) FUNCIONA -- INSERTA LOS DATOS DEL EXCEL A LA BASE DE DATOS
        if ($request->hasFile('archivo')) {
            DatosTemporales::truncate(); // Quitamos todos los datos de la tabla
            $archivo = $request->file('archivo');
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
            $erroresUnicidad = []; // Arreglo para almacenar registros con errores de unicidad
            $primerRegistro = true;
            $contador = 1;

            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $dataDetalle = array(
                    'rfc' => $rowData[2],
                    'nombre_completo' => $rowData[3],
                    'curp' => 'curp-'.$contador,
                    'cuip' => 'cuip-'.$contador,                    
                );
                $contador++;

                try {
                    // INSERTAMOS EN LA TABLA datos_temporales
                    if (!$primerRegistro) {
                        DatosTemporales::create($dataDetalle);
                    } else {
                        $primerRegistro = false; // Cambiar el valor después del primer ciclo
                    } 

                } catch (\Illuminate\Database\QueryException $e) {
                    // Verificar si el error es de unicidad (por clave duplicada)
                    if ($e->errorInfo[1] == 1062) {
                        $erroresUnicidad[] = $dataDetalle; // Almacenar el registro con error
                    }

                    if ($e->errorInfo[1] == 1048) { // valores vacios en unique
                        $erroresUnicidad[] = $dataDetalle; // Almacenar el registro con error
                    }

                    if ($e->errorInfo[1] == 1451) { // error en clave externa
                        $erroresUnicidad[] = $dataDetalle; // Almacenar el registro con error
                    }
                }
            }

            // Función para normalizar nombre: quita espacios, cambia ñ → n, pone en minúsculas
            //$normalizarNombre = function ($nombre) {
            //    $nombre = preg_replace('/\s+/', '', $nombre); // sin espacios
            //    $nombre = str_replace(['ñ', 'Ñ'], 'N', $nombre); // ñ → n
            //    $nombre = mb_strtolower($nombre); // todo minúscula
            //    return $nombre;
            //};
            
            $normalizarNombre = function ($nombre) {
                // Quitar espacios
                $nombre = preg_replace('/\s+/', '', $nombre);

                // Convertir acentos y caracteres especiales
                $nombre = strtr(
                    $nombre,
                    [
                        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U',
                        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
                        'Ñ' => 'N', 'ñ' => 'n'
                    ]
                );

                // Convertir a minúsculas (después de reemplazo)
                $nombre = mb_strtolower($nombre);

                return $nombre;
            };

            // Reseteamos el campo de temporal_captura de la tabla SOCIOS
            Socios::query()->update(['temporal_captura' => '']);

        
            // Obtener registros de DatosTemporales y Socios
            /*$datosTemporales = DatosTemporales::pluck('nombre_completo')->map(function ($nombre) {
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
            */

            // 1. Genera arrays asociativos [normalizado => original]
            $datosTemporales = DatosTemporales::pluck('nombre_completo')->mapWithKeys(function ($nombre) use ($normalizarNombre) {
                return [$normalizarNombre($nombre) => $nombre];
            })->toArray();

            $socios = Socios::pluck('nombre_completo')->mapWithKeys(function ($nombre) use ($normalizarNombre) {
                return [$normalizarNombre($nombre) => $nombre];
            })->toArray();

            // 2. Compara claves normalizadas
            $noDB = array_diff(array_keys($datosTemporales), array_keys($socios));
            $noFinanciero = array_diff(array_keys($socios), array_keys($datosTemporales));

            // 3. Recupera los valores originales para hacer los WHERE
            $originalesNoDB = array_values(array_intersect_key($datosTemporales, array_flip($noDB)));
            $originalesNoFinanciero = array_values(array_intersect_key($socios, array_flip($noFinanciero)));

            // 4. Actualiza en base a los nombres originales
            DatosTemporales::whereIn('nombre_completo', $originalesNoDB)->update(['temporal_captura' => 'No en DB']);
            Socios::whereIn('nombre_completo', $originalesNoFinanciero)->update(['temporal_captura' => 'No en Financiero']);
            

            return response()->json('success');
        }
    
        return response()->json(['error' => 'No se ha proporcionado ningún archivo.']);

    }

    public function excelNoDBAhorro(){
        $datosTemporales = DatosTemporales::where('temporal_captura', '=', 'No en DB')
        //whereNotNull('temporal_captura')
        ->get();

        $resultadoFinal = [
            'data' => $datosTemporales
        ];

        return json_encode($resultadoFinal);
    }

    public function excelNoFinancieroAhorro(){
        $socios = Socios::where('temporal_captura', '=', 'No en Financiero')
        //whereNotNull('temporal_captura')
        ->get();
        
        $resultadoFinal = [
            'data' => $socios
        ];

        return json_encode($resultadoFinal);
    }

    public function excelImportaAportacion(){
        $socios = Socios::whereNull('temporal_captura')
            ->orWhere('temporal_captura', '=', '')
            ->get();

        $totalSocios = $socios->count(); // Total de registros
        $montoTotal = $totalSocios * 200; // Multiplicación
        
        $resultadoFinal = [
            'data' => $socios,
            'total_socios' => $totalSocios,
            'monto_total' => $montoTotal
        ];

        return json_encode($resultadoFinal);
    }

}
