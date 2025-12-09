<?php

namespace App\Http\Controllers;

use App\Models\ExcelSocio;
use App\Models\Socios;
use App\Models\DatosTemporales;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelSocioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:cargar-socios-excel', ['only'=>['create', 'store']]);

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
        $socios = true;

        if (Socios::count() > 0) {
            $socios = false;
        } else {
            $socios = true;
        }

        return view('excel_socios.create',compact('socios'));
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            // COPIAMOS EL CONTENIDO DE LA TABLA datos_temporales A LA TABLA socios
            $registrosOrigen = DatosTemporales::all();

            // Inserta los registros en la tabla destino
            foreach ($registrosOrigen as $registro) {
                if($registro->sector_id == 'SECTOR PLAZA'){
                    $sector = 1;
                }else if ( $registro->sector_id == ''){
                    $sector = 17;
                }

                $categoria = 9;

                switch ($registro->categoria_id) {
                    case 'INSPECTOR':
                        $categoria = 8;
                        break;
                    case 'OFICIAL':
                        $categoria = 6;
                        break;
                    case 'POLICIA 1/o.':
                        $categoria = 2;
                        break;
                    case 'POLICIA 2/o.':
                        $categoria = 3;
                        break;
                    case 'POLICIA 3/o.':
                        $categoria = 4;
                        break;
                    case 'SUBINSPECTOR':
                        $categoria = 7;
                        break;
                    default:
                        $categoria = 9;
                        break;
                }

                Socios::create([
                    'sector_id' => $sector,
                    'categoria_id' => $categoria,
                    'num_socio' => $registro->num_socio,
                    'nombre' => trim($registro->nombre),
                    'apellido_paterno' => trim($registro->apellido_paterno),
                    'apellido_materno' => trim($registro->apellido_materno),
                    'nombre_completo' => trim($registro->nombre_completo),
                    'rfc' => trim($registro->rfc),
                    'fecha_alta' => $registro->fecha_alta,
                    'telefono' => $registro->telefono,
                    'domicilio' => $registro->domicilio,
                    'curp' => trim($registro->curp),
                    'cuip' => trim($registro->cuip),
                    'estado_civil' => $registro->estado_civil,
                    'contacto_emergencia' => $registro->contacto_emergencia,
                    'telefono_emergencia' => $registro->telefono_emergencia,
                    'tipo_sangre' => $registro->tipo_sangre,
                    'lugar_origen' => $registro->lugar_origen,
                    'alta_coorporacion' => $registro->alta_coorporacion,
                    'compania' => $registro->compania,
                    'batallon' => $registro->batallon,
                ]);
            }

            \DB::commit();

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

    public function show(ExcelSocio $excelSocio)
    {
        //
    }

    public function edit(ExcelSocio $excelSocio)
    {
        //
    }

    public function update(Request $request, ExcelSocio $excelSocio)
    {
        //
    }

    public function destroy(ExcelSocio $excelSocio)
    {
        //
    }

    public function leerArchivoExcel(Request $request)
    {
        // Habilitar el modo de depuración
       /* ini_set('display_errors', '1');
        error_reporting(E_ALL);*/

       /*
       // OBTIENE LOS VALORES DEL EXCEL Y LO MUESTRA EN LA VISTA A TRAVEZ DE UN DATATABLE
        $columnMapping = [
            1,
            21, //'sector_id'
            22, //'categoria_id'
            0, //'num_socio'
            2, //'nombre'
            3, //'apellido_paterno'
            4, //'apellido_materno'
            6, //'rfc'
            7, //'fecha_alta'
            8, //'telefono'
            9, //'domicilio'
            10, //'curp'
            11, //'cuip'
            12, //'estado_civil'
            13, //'contacto_emergencia'
            14, //'telefono_emergencia'
            16, //'tipo_sangre'
            17, //'lugar_origen'
            18, //'alta_coorporacion'
            19, //'compania'
            20, //'batallon'

        ];

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
    
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $data = [];
            
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                $registro = [];
                foreach ($columnMapping as $indiceColumna) {
                    if ($indiceColumna === 4) {
                        $registro[] = $rowData[4]; // Valor columna 4
                        $concatenatedValue = $rowData[2] . ' ' . $rowData[3] . ' ' . $rowData[4];
                        $registro[] = $concatenatedValue; // Valor concatenado
                        
                    } else {
                        $registro[] = $rowData[$indiceColumna];
                    }
                }
                $data[] = $registro;
            }
    
            return response()->json($data);
        }
    
        return response()->json(['error' => 'No se ha proporcionado ningún archivo.']);*/

        /*if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
    
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $data = [];
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Verificar si el registro ya existe en la base de datos
                $existingRecord = Socios:://where('nombre_completo', $rowData['nomcompleto'])
                where('rfc', $rowData[6])
                ->where('telefono', $rowData[8])
                ->where('curp', $rowData[10])
                ->where('cuip', $rowData[11])
                ->first();
                if ($existingRecord) {
                    $rowData['duplicado'] = 'Existente';
                } else {
                    $rowData['duplicado'] = 'Nuevo';
                }
    
                $data[] = $rowData;
            }
    
            return response()->json($data);
        }*/



        // -- FUNCIONA -- INSERTA LOS DATOS DEL EXCEL A LA BASE DE DATOS
        /*if ($request->hasFile('archivo')) {
            DatosTemporales::truncate(); // quitamos todos los datos de la tabla
            $archivo = $request->file('archivo');
        
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
        
            $data = [];
            $primerRegistro = true;
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Insertar datos en la tabla temporal
                if (!$primerRegistro) {
                    DatosTemporales::insert([
                        'sector_id' => $rowData[21],
                        'categoria_id' => $rowData[22],
                        'num_socio' => $rowData[0],
                        'nombre' => $rowData[2],
                        'apellido_paterno' => $rowData[3],
                        'apellido_materno' => $rowData[4],
                        'nombre_completo' => $rowData[3].' '. $rowData[4].' '.$rowData[2],
                        'rfc' => $rowData[6],
                        //'fecha_alta' => $rowData[7],//$fechaAlta,
                        //'fecha_alta' => $fechaAlta,//$fechaAlta,
                        'telefono' => $rowData[8],
                        'domicilio' => $rowData[9],
                        'curp' => $rowData[10],
                        'cuip' => $rowData[11],
                        'estado_civil' => $rowData[12],
                        'contacto_emergencia' => $rowData[13],
                        'telefono_emergencia' => $rowData[14],
                        'tipo_sangre' => $rowData[15].' '.$rowData[16],
                        'lugar_origen' => $rowData[17],
                        //'alta_coorporacion' => $rowData[18],
                        'compania' => $rowData[19],
                        'batallon' => $rowData[20],
                    ]);
                } else {
                    $primerRegistro = false; // Cambiar el valor después del primer ciclo
                }        
                $data[] = $rowData;
            }
        
            // Realizar operaciones adicionales en la tabla temporal si es necesario
        
            // Obtener los datos de la tabla temporal y retornarlos en formato JSON
            $tablaTemporalData = DatosTemporales::get();
            return response()->json($tablaTemporalData);
        }
    
        return response()->json(['error' => 'No se ha proporcionado ningún archivo.']);*/


        // -- ( 2 ) FUNCIONA -- INSERTA LOS DATOS DEL EXCEL A LA BASE DE DATOS
        if ($request->hasFile('archivo')) {
            DatosTemporales::truncate(); // Quitamos todos los datos de la tabla
            $archivo = $request->file('archivo');
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
            $erroresUnicidad = []; // Arreglo para almacenar registros con errores de unicidad
            $primerRegistro = true;
            $i = 0;
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $dataDetalle = array(
                    'sector_id' => $rowData[20],
                    'categoria_id' => $rowData[21],
                    'num_socio' =>$i,
                    'nombre' => $rowData[1],
                    'apellido_paterno' => $rowData[2],
                    'apellido_materno' => $rowData[3],
                    'nombre_completo' => $rowData[2].' '. $rowData[3].' '.$rowData[1],
                    'rfc' => $rowData[5],
                    //'fecha_alta' => $rowData[7],//$fechaAlta,
                    //'fecha_alta' => $fechaAlta,//$fechaAlta,
                    'telefono' => $rowData[7],
                    'domicilio' => $rowData[8],
                    'curp' => $rowData[9],
                    'cuip' => $rowData[10],
                    'estado_civil' => $rowData[11],
                    'contacto_emergencia' => $rowData[12],
                    'telefono_emergencia' => $rowData[13],
                    'tipo_sangre' => $rowData[14].' '.$rowData[15],
                    'lugar_origen' => $rowData[16],
                    //'alta_coorporacion' => $rowData[18],
                    'compania' => $rowData[18],
                    'batallon' => $rowData[19],                      
                );
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
                $i++;
            }
        
            // Obtener los datos de la tabla temporal y retornarlos en formato JSON
            $tablaTemporalData = DatosTemporales::get();
            
            // Agregar los registros con errores al resultado final
            $resultadoFinal = [
                'data' => $tablaTemporalData,
                'erroresUnicidad' => $erroresUnicidad,
            ];
        
            //return response()->json($erroresUnicidad);
            return response()->json($resultadoFinal);
        }
    
        return response()->json(['error' => 'No se ha proporcionado ningún archivo.']);

    }
}
