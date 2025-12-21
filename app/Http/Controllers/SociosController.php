<?php

namespace App\Http\Controllers;

use App\Models\Socios;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Sectores;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Beneficiario;
use App\Models\SectorCategoria;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Exception;
use PDF;

class SociosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:ver-socio|crear-socio|editar-socio|borrar-socio', ['only'=>['index']]);
        $this->middleware('permission:crear-socio', ['only'=>['create', 'store']]);
        $this->middleware('permission:editar-socio', ['only'=>['edit', 'update']]);
        $this->middleware('permission:borrar-socio', ['only'=>['show','destroy']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        return view('socios.index');
    }

    public function create()
    {
        $socios = new Socios;
        $categorias = SectorCategoria::where('tipo', 'CATEGORÍA')->get();
        $sectores =  SectorCategoria::where('tipo', 'SECTOR')->get();
        $estadoCivil = ['-', 'SOLTERO (A)', 'CASADO (A)', 'DIVORCIADO (A)', 'SEPARADO (A)', 'VIUDO (A)', 'UNIÓN LIBRE', 'CONCUBINATO', 'TRÁMITE DE DIVORCIO'];
        $beneficiarios = [];

        return view('socios.create', compact('socios', 'categorias', 'sectores', 'beneficiarios','estadoCivil'));
    }

    public function store(Request $request)
    {
        $socios = new Socios();

        $names  = array(
            'sector_id' => 'SECTOR',
            'categoria_id' => 'CATEGORÍA',
            'nombre' => 'NOMBRE(S)',
            'apellido_paterno' => 'APELLIDO PATERNO',
            'apellido_materno' => 'APELLIDO MATERNO',
            'nombre_completo' => 'NOMBRE',
            'rfc' => 'RFC',
            'telefono' => 'TELÉFONO',
            'domicilio' => 'DOMICILIO',
            'curp' => 'CURP',
            'cuip' => 'CUIP',
            'estado_civil' => 'ESTADO CIVIL',
            'contacto_emergencia' => 'EMERGENCIAS, COMUNICARSE CON',
            'telefono_emergencia' => 'TELÉFONO DE EMERGENCIA',
            'tipo_sangre' => 'TIPO DE SANGRE',
            'lugar_origen' => 'LUGAR DE ORIGEN',
            'alta_coorporacion' => 'ALTA A LA COORPORACIÓN',
            'compania' => 'COMPAÑIA',
            'batallon' => 'BATALLÓN',
        );

        $validator = Validator::make($request->all(), [
            'sector_id' => 'required|numeric|gt:0',
            'categoria_id' => 'required|numeric|gt:0',
            'nombre' => 'required|string|min:2|max:50',
            'apellido_paterno' => 'nullable|string|max:50',
            'apellido_materno' => 'nullable|string|max:50',
            'nombre_completo' => 'string',
            'rfc' => ['required', 'string', 'max:13'],
            'telefono' => ['required', 'string', 'min:7', 'max:35'],
            'domicilio' => 'required|string|min:20|max:90',
            'curp' => ['required', 'string', 'max:18'],
            'cuip' => ['nullable', 'string', 'min:18'],
            'estado_civil' => 'required|string|min:1',
            'contacto_emergencia' => 'required|string|min:7',
            'telefono_emergencia' => 'required|string|min:7|max:35',
            'tipo_sangre' => 'required|string|min:2',
            'lugar_origen' => 'required|string|min:15|max:90',
            'alta_coorporacion' => 'required|date_format:d/m/Y',
            'compania' => 'required|string|min:1',
            'batallon' => 'required|string|min:1',
        ], [], $names);

        $validator->after(function ($validator) use ($request) {
            $apellidoPaterno = strtoupper(trim($request->input('apellido_paterno')));
            $apellidoMaterno = strtoupper(trim($request->input('apellido_materno')));
            $nombre = strtoupper(trim($request->input('nombre')));
            $nombreCompleto = trim("$apellidoPaterno $apellidoMaterno $nombre");

            $rfc = strtoupper(trim($request->input('rfc')));
            $curp = strtoupper(trim($request->input('curp')));
            $cuip = strtoupper(trim($request->input('cuip')));

            // Validar si ya existe un socio exacto con estos tres campos
            $posibles = Socios::where('nombre_completo', $nombreCompleto)->get();
            foreach ($posibles as $posible) {
                if (
                    $posible->rfc === $rfc &&
                    $posible->curp === $curp &&
                    $posible->cuip === $cuip
                ) {
                    $validator->errors()->add('nombre_completo', 'Este socio ya está registrado.');
                    return;
                }
            }

            // Validaciones individuales en sus respectivos campos
            if (Socios::where('rfc', $rfc)->exists()) {
                $validator->errors()->add('rfc', 'El RFC ya se encuentra registrado con otro socio.');
            }

            if (Socios::where('curp', $curp)->exists()) {
                $validator->errors()->add('curp', 'La CURP ya se encuentra registrada con otro socio.');
            }

            if (Socios::where('cuip', $cuip)->exists()) {
                $validator->errors()->add('cuip', 'La CUIP ya se encuentra registrada con otro socio.');
            }

            // Formatear y guardar en el request
            $request->merge([
                'nombre_completo' => $nombreCompleto,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'nombre' => $nombre,
            ]);

            // Mantener campos dinámicos (beneficiarios)
            $oldValues = $request->old();
            $oldValues['nombre_beneficiario'] = $request->input('nombre_beneficiario', []);
            $oldValues['domicilio_beneficiario'] = $request->input('domicilio_beneficiario', []);
            $oldValues['telefono_beneficiario'] = $request->input('telefono_beneficiario', []);
            $oldValues['porcentaje_beneficiario'] = $request->input('porcentaje_beneficiario', []);
            $validator->setData($oldValues);
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // VALIDACIÓN DE BENEFICIARIOS
        $beneficiarios = $request->input('nombre_beneficiario', []);
        $domicilios = $request->input('domicilio_beneficiario', []);
        $telefonos = $request->input('telefono_beneficiario', []);
        $porcentajes = $request->input('porcentaje_beneficiario', []);

        $totalPorcentaje = 0;
        $erroresBeneficiarios = [];

        foreach ($beneficiarios as $i => $nombre) {
            $nombre = trim($nombre);
            $domicilio = trim($domicilios[$i] ?? '');
            $telefono = trim($telefonos[$i] ?? '');
            $porcentaje = isset($porcentajes[$i]) ? (float) $porcentajes[$i] : null;

            if (empty($nombre) || empty($domicilio) || empty($telefono) || is_null($porcentaje)) {
                $erroresBeneficiarios[] = "Todos los campos del beneficiario #" . ($i + 1) . " son obligatorios.";
            }

            if (!is_numeric($porcentaje) || $porcentaje <= 0 || $porcentaje > 100) {
                $erroresBeneficiarios[] = "El porcentaje del beneficiario #" . ($i + 1) . " es inválido.";
            }

            $totalPorcentaje += $porcentaje;
        }

        if (count($beneficiarios) > 0 && abs($totalPorcentaje - 100) > 0.01) {
            $erroresBeneficiarios[] = "La suma total de los porcentajes de beneficiarios debe ser exactamente 100%.";
        }

        if (count($erroresBeneficiarios) > 0) {
            return redirect()->back()
                ->withErrors($erroresBeneficiarios)
                ->withInput();
        }

        try {
            \DB::beginTransaction();
            $fecha = $request->input('alta_coorporacion');
            $nuevaFecha = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');

            $socios->sector_id = $request->input('sector_id');
            $socios->categoria_id = $request->input('categoria_id');
            $socios->nombre = strtoupper(trim($request->input('nombre')));
            $socios->apellido_paterno = strtoupper(trim($request->input('apellido_paterno')));
            $socios->apellido_materno = strtoupper(trim($request->input('apellido_materno')));
            $socios->nombre_completo = strtoupper(trim($request->input('apellido_paterno') . ' ' . $request->input('apellido_materno') . ' ' . $request->input('nombre')));
            $socios->rfc = strtoupper(trim($request->input('rfc')));
            $socios->fecha_alta = Carbon::now();
            $socios->telefono = $request->input('telefono');
            $socios->domicilio = strtoupper($request->input('domicilio'));
            $socios->curp = strtoupper(trim($request->input('curp')));
            $socios->cuip = strtoupper(trim($request->input('cuip')));
            $socios->estado_civil = strtoupper($request->input('estado_civil'));
            $socios->contacto_emergencia = strtoupper($request->input('contacto_emergencia'));
            $socios->telefono_emergencia = $request->input('telefono_emergencia');
            $socios->tipo_sangre = strtoupper($request->input('tipo_sangre'));
            $socios->lugar_origen = strtoupper($request->input('lugar_origen'));
            $socios->alta_coorporacion = $nuevaFecha; //$request->input('alta_coorporacion');
            $socios->compania = strtoupper($request->input('compania'));
            $socios->batallon = strtoupper($request->input('batallon'));
            $socios->saldo = 0;
            $socios->inscripcion = 0;
            $socios->tipo = 'PENDIENTE';
            $socios->save();

            // Luego asignas el num_socio con el ID recién generado
            Socios::where('id', $socios->id)->update([
                'num_socio' => 'SOC-' . $socios->id
            ]);

            // Guardar historial antes de cambiar
            $socios->ajustes()->create([
                'tipo' => 'PENDIENTE',
                'fecha' => $request->input('fecha_baja') ?? now(),
                'observaciones' => $request->input('observaciones') ?? 'Registro inicial como PENDIENTE',
                'wci' => auth()->id(),
            ]);

            // INSERTAMOS LOS DATOS DE BENEFICIARIO EN CASO DE EXISTIR
            $nuevoID = $socios->id;
            if (!empty($request->nombre_beneficiario ?? null)) {
                foreach ($request->nombre_beneficiario as $key => $value) {
                    Beneficiario::create([
                        'socios_id' => $nuevoID,
                        'nombre_beneficiario' => strtoupper($request->nombre_beneficiario[$key]),
                        'domicilio_beneficiario' => strtoupper($request->domicilio_beneficiario[$key]),
                        'telefono_beneficiario' => $request->telefono_beneficiario[$key],
                        'porcentaje_beneficiario' => $request->porcentaje_beneficiario[$key],
                    ]);
                }
            }

            \DB::commit();

            session()->flash('swal', [
                'icon' => "success",
                'title' => "Operación correcta",
                'text' => "El socio se creó correctamente.",
            ]);

            return redirect()->route('admin.socios.index');
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show($id)
    {
        $socios = Socios::findorfail($id);
        $categorias = SectorCategoria::where('tipo', 'CATEGORÍA')->get();
        $sectores =  SectorCategoria::where('tipo', 'SECTOR')->get();
        $estadoCivil = ['-', 'SOLTERO (A)', 'CASADO (A)', 'DIVORCIADO (A)', 'SEPARADO (A)', 'VIUDO (A)', 'UNIÓN LIBRE', 'CONCUBINATO', 'TRÁMITE DE DIVORCIO'];
        $tipoValues = ['BAJA DE LA CORPORACIÓN','BAJA DE LA CAJA','FALLECIMIENTO'];

        return view('socios.delete', compact('socios', 'categorias', 'sectores','tipoValues','estadoCivil'));
    }

    public function edit($id)
    {
        $socios = Socios::findorfail($id);
        $categorias = SectorCategoria::where('tipo', 'CATEGORÍA')->get();
        $sectores =  SectorCategoria::where('tipo', 'SECTOR')->get();
        $estadoCivil = ['-', 'SOLTERO (A)', 'CASADO (A)', 'DIVORCIADO (A)', 'SEPARADO (A)', 'VIUDO (A)', 'UNIÓN LIBRE', 'CONCUBINATO', 'TRÁMITE DE DIVORCIO'];
        $beneficiarios = $socios->beneficiarios()->where('activo', 1)->get(); //$socios->beneficiarios; // obtengo los beneficiarios del socio, a travez de la relacion
        $tipoValues = ['ACTIVO','DEVOLUCIONES','BAJA DE LA CORPORACIÓN','BAJA DE LA CAJA','FALLECIMIENTO','PENDIENTE','TRÁMITE'];


        //$socios->alta_coorporacion = Carbon::createFromFormat('d/m/Y', $socios->alta_coorporacion)->format('Y-m-d'); //$socios->alta_coorporacion->format('d/m/Y');

        return view('socios.edit', compact('socios', 'categorias', 'sectores', 'beneficiarios','tipoValues', 'estadoCivil'));
    }

    public function update(Request $request, $id)
    {
        $socios = Socios::findorfail($id);
        $socioId = $socios->id;
        $nuevoID = $socios->id;

        $names  = array(
            'sector_id' => 'SECTOR',
            'categoria_id' => 'CATEGORÍA',
            'nombre' => 'NOMBRE(S)',
            'apellido_paterno' => 'APELLIDO PATERNO',
            'apellido_materno' => 'APELLIDO MATERNO',
            'nombre_completo' => 'NOMBRE',
            'rfc' => 'RFC',
            'telefono' => 'TELÉFONO',
            'domicilio' => 'DOMICILIO',
            'curp' => 'CURP',
            'cuip' => 'CUIP',
            'estado_civil' => 'ESTADO CIVIL',
            'contacto_emergencia' => 'EMERGENCIAS, COMUNICARSE CON',
            'telefono_emergencia' => 'TELÉFONO DE EMERGENCIA',
            'tipo_sangre' => 'TIPO DE SANGRE',
            'lugar_origen' => 'LUGAR DE ORIGEN',
            'alta_coorporacion' => 'ALTA A LA COORPORACIÓN',
            'compania' => 'COMPAÑIA',
            'batallon' => 'BATALLÓN',
        );

        $validator = Validator::make($request->all(), [
            'sector_id' => 'required|numeric|gt:0',
            'categoria_id' => 'required|numeric|gt:0',
            'nombre' => 'required|string|min:2|max:50',
            'apellido_paterno' => 'nullable|string|max:50',
            'apellido_materno' => 'nullable|string|max:50',
            'nombre_completo' => [
                'string',
                function ($attribute, $value, $fail) use ($request, $socioId) {
                    $apellidoPaterno = strtoupper(trim($request->input('apellido_paterno')));
                    $apellidoMaterno = strtoupper(trim($request->input('apellido_materno')));
                    $nombre = strtoupper(trim($request->input('nombre')));
                    $nombreCompleto = "$apellidoPaterno $apellidoMaterno $nombre";

                    $rfc = strtoupper(trim($request->input('rfc')));
                    $curp = strtoupper(trim($request->input('curp')));
                    $cuip = strtoupper(trim($request->input('cuip')));

                    // Buscar registros similares por nombre completo
                    $posibles = Socios::where('nombre_completo', $nombreCompleto)->where('id', '!=', $socioId)->get();

                    foreach ($posibles as $posible) {
                        if ($posible->rfc === $rfc && $posible->curp === $curp && $posible->cuip === $cuip) {
                            $fail("Este socio ya está registrado.");
                            return;
                        }
                    }

                    $duplicadoRfc = Socios::where('rfc', $rfc)
                        ->where('id', '!=', $socioId)
                        ->exists();

                    $duplicadoCurp = Socios::where('curp', $curp)
                        ->where('id', '!=', $socioId)
                        ->exists();

                    $duplicadoCuip = Socios::where('cuip', $cuip)
                        ->where('id', '!=', $socioId)
                        ->exists();

                    if ($duplicadoRfc) {
                        $fail("El RFC ya se encuentra registrado con otro socio.");
                        return;
                    }

                    if ($duplicadoCurp) {
                        $fail("La CURP ya se encuentra registrada con otro socio.");
                        return;
                    }

                    if ($duplicadoCuip) {
                        $fail("La CUIP ya se encuentra registrada con otro socio.");
                        return;
                    }

                    // Merge campos ya formateados
                    $request->merge([
                        'nombre_completo' => $nombreCompleto,
                        'apellido_paterno' => $apellidoPaterno,
                        'apellido_materno' => $apellidoMaterno,
                        'nombre' => $nombre,
                    ]);
                },
            ],

            'rfc' => ['required', 'string', 'max:13'],
            'telefono' => ['required', 'string', 'min:7', 'max:35'], //, 'unique:socios,telefono,' . $socios->id],
            'domicilio' => 'required|string|min:20|max:90',
            'curp' => ['required', 'string', 'max:18'],
            'cuip' => ['nullable', 'string', 'min:18'],
            'estado_civil' => 'required|string|min:1',
            'contacto_emergencia' => 'required|string|min:7',
            'telefono_emergencia' => 'required|string|min:7|max:35',
            'tipo_sangre' => 'required|string|min:2',
            'lugar_origen' => 'required|string|min:15|max:90',
            'alta_coorporacion' => 'required|date_format:d/m/Y',
            'compania' => 'required|string|min:1',
            'batallon' => 'required|string|min:1',
        ], [], $names);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // VALIDACIÓN DE BENEFICIARIOS
        $beneficiarios = $request->input('nombre_beneficiario', []);
        $domicilios = $request->input('domicilio_beneficiario', []);
        $telefonos = $request->input('telefono_beneficiario', []);
        $porcentajes = $request->input('porcentaje_beneficiario', []);

        $totalPorcentaje = 0;
        $erroresBeneficiarios = [];

        foreach ($beneficiarios as $i => $nombre) {
            $nombre = trim($nombre);
            $domicilio = trim($domicilios[$i] ?? '');
            $telefono = trim($telefonos[$i] ?? '');
            $porcentaje = isset($porcentajes[$i]) ? (float) $porcentajes[$i] : null;

            if (empty($nombre) || empty($domicilio) || empty($telefono) || is_null($porcentaje)) {
                $erroresBeneficiarios[] = "Todos los campos del beneficiario #" . ($i + 1) . " son obligatorios.";
            }

            if (!is_numeric($porcentaje) || $porcentaje <= 0 || $porcentaje > 100) {
                $erroresBeneficiarios[] = "El porcentaje del beneficiario #" . ($i + 1) . " es inválido.";
            }

            $totalPorcentaje += $porcentaje;
        }

        // Si hay beneficiarios, validar que sumen 100%
        if (count($beneficiarios) > 0 && abs($totalPorcentaje - 100) > 0.01) {
            $erroresBeneficiarios[] = "La suma total de los porcentajes de beneficiarios debe ser exactamente 100%.";
        }

        if (count($erroresBeneficiarios) > 0) {
            return redirect()->back()
                ->withErrors($erroresBeneficiarios)
                ->withInput();
        }

        try {
            \DB::beginTransaction();
            $fecha = $request->input('alta_coorporacion');
            $nuevaFecha = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');

            // Convertir la fecha solo si viene con valor
            $fecha_baja = null;
            if (!empty($request->input('fecha_baja'))) {
                $fecha_baja = Carbon::createFromFormat('d/m/Y', $request->input('fecha_baja'))->format('Y-m-d');
            }

            // Guardar historial antes de cambiar
            $fechaBaja = $request->fecha_baja
                ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha_baja)->format('Y-m-d')
                : now()->format('Y-m-d');
            $socios->ajustes()->create([
                'tipo' => $request->tipo,
                'fecha' => $fechaBaja,
                'observaciones' => $request->observaciones,
                'wci' => auth()->id(),
            ]);

            $socios->update([
                'sector_id' => $request->input('sector_id'),
                'categoria_id' => $request->input('categoria_id'),
                'nombre' => strtoupper(trim($request->input('nombre'))),
                'apellido_paterno' => strtoupper(trim($request->input('apellido_paterno'))),
                'apellido_materno' => strtoupper(trim($request->input('apellido_materno'))),
                'nombre_completo' => strtoupper(trim($request->input('apellido_paterno') . ' ' . $request->input('apellido_materno') . ' ' . $request->input('nombre'))),
                'rfc' => strtoupper(trim($request->input('rfc'))),
                'telefono' => $request->input('telefono'),
                'domicilio' => strtoupper($request->input('domicilio')),
                'curp' => strtoupper(trim($request->input('curp'))),
                'cuip' => strtoupper(trim($request->input('cuip'))),
                'estado_civil' => strtoupper($request->input('estado_civil')),
                'contacto_emergencia' => strtoupper($request->input('contacto_emergencia')),
                'telefono_emergencia' => $request->input('telefono_emergencia'),
                'tipo_sangre' => strtoupper($request->input('tipo_sangre')),
                'lugar_origen' => strtoupper($request->input('lugar_origen')),
                'alta_coorporacion' => $nuevaFecha,
                'compania' => strtoupper($request->input('compania')),
                'batallon' => strtoupper($request->input('batallon')),
                'tipo' => $request->input('tipo'),
                'fecha_baja' => $fecha_baja,
                'observaciones' => strtoupper($request->input('observaciones')),
            ]);

            // ACTUALIZAMOS EN NOMBRE EN LA TABLA USERS
            User::where('id', $nuevoID)->update([
                'name' =>  strtoupper($request->get('apellido_paterno')) . ' ' . strtoupper($request->get('apellido_materno')). ' ' . strtoupper($request->get('nombre')),
            ]);

            // ACTUALIZAMOS E INSERTAMOS LOS DATOS DE BENEFICIARIO EN CASO DE EXISTIR
            if (!empty($request->nombre_beneficiario)) {
                // Obtén los IDs de los beneficiarios existentes del socio
                $idsExistentes = $socios->beneficiarios->pluck('id')->toArray();

                foreach ($request->nombre_beneficiario as $key => $value) {
                    $data = [
                        'nombre_beneficiario' => strtoupper($request->nombre_beneficiario[$key]),
                        'domicilio_beneficiario' => strtoupper($request->domicilio_beneficiario[$key]),
                        'telefono_beneficiario' => $request->telefono_beneficiario[$key],
                        'porcentaje_beneficiario' => $request->porcentaje_beneficiario[$key],
                    ];

                    // Si el ID está presente en la solicitud, actualiza el registro
                    if ($request->socios_id[$key] > 0) {
                        $beneficiario = Beneficiario::findOrFail($request->socios_id[$key]);
                        $beneficiario->update($data);
                        // Elimina el ID de la lista de IDs existentes
                        unset($idsExistentes[array_search($beneficiario->id, $idsExistentes)]);
                    } else {
                        // Crea un nuevo beneficiario
                        $data['socios_id'] = $socios->id;
                        Beneficiario::create($data);
                    }
                }

                // Desactiva los beneficiarios que no se encontraron en la solicitud
                if (!empty($idsExistentes)) {
                    Beneficiario::whereIn('id', $idsExistentes)->update(['activo' => 0]);
                }
            }else {
                // Si no hay beneficiarios en el request, desactiva todos los actuales
                Beneficiario::where('socios_id', $socios->id)->update(['activo' => 0]);
            }

            \DB::commit();

            session()->flash('swal', [
                'icon' => "success",
                'title' => "Operación correcta",
                'text' => "Datos modificados correctamente.",
            ]);

            return redirect()->route('admin.socios.index');
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function destroy(Request $request, $id)
    {
        $socios = Socios::findorfail($id);
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // Convertir la fecha solo si viene con valor
        $fecha_baja = null;
        if (!empty($request->input('fecha_baja'))) {
            $fecha_baja = Carbon::createFromFormat('d/m/Y', $request->input('fecha_baja'))->format('Y-m-d');
        }

        $socios->update([
            'num_socio' => $socios->num_socio . '-' . substr(str_shuffle($permitted_chars), 0, 5),
            'nombre_completo' => $socios->nombre_completo . '-' . substr(str_shuffle($permitted_chars), 0, 5),
            'rfc' => $socios->rfc . '-' . substr(str_shuffle($permitted_chars), 0, 5),
            'telefono' => $socios->telefono . '-' . substr(str_shuffle($permitted_chars), 0, 5),
            'curp' => $socios->curp . '-' . substr(str_shuffle($permitted_chars), 0, 5),
            'cuip' => $socios->cuip . '-' . substr(str_shuffle($permitted_chars), 0, 5),
            'tipo' => $request->input('tipo'),
            'fecha_baja' => $fecha_baja,
            'observaciones' => strtoupper($request->input('observaciones')),
            'activo'  =>  0,
        ]);


        return response()->json([
            'swal' => [
                'icon' => "success",
                'title' => "Operación correcta",
                'text' => "El socio ha sido eliminado correctamente.",
            ],
            'redirect' => route('admin.socios.index'),
        ]);
    }

    public function guardarFoto(Request $request)
    {
        try {
            // Obtener el valor de usuario_id desde la solicitud
            $usuarioId = $request->input('usuario_id');

            // Verificar si el usuario ya tiene una imagen almacenada
            $usuario = Socios::find($usuarioId);

            //if (!empty($usuario->photo_path)) {
            //    // Si tiene una imagen almacenada, elimina la imagen anterior
            //    File::delete(public_path('/foto_socios/' . $usuario->photo_path));
            //}
            $photoPath = $usuario->photo_path;
            if (!empty($photoPath)) {
                // Combinar la ruta relativa con el disco 'foto_socios' para obtener la ruta completa
                $fullPath = Storage::disk('foto_socios')->path($photoPath);

                // Verificar si el archivo existe en el disco 'foto_socios'
                if (Storage::disk('foto_socios')->exists($photoPath)) {
                    // Eliminar el archivo utilizando File::delete
                    File::delete($fullPath);
                }
            }

           /* if (File::exists($request->file('photo'))) {
                $image = $request->file('photo');
                $input['imagename'] = uniqid() . '.' . $image->extension();

                $filePath = public_path('/foto_socios/');
                //$filePath = '/public_html/sspo/foto_socios/';
               // return response()->json(['sss' => $filePath]);
                $image->move($filePath, $input['imagename']);

                // Actualizar la ruta de la imagen en la base de datos
                Socios::where('id', $usuarioId)->update(['photo_path' => $input['imagename']]);

            }*/

            if (File::exists($request->file('photo'))) {
                $image = $request->file('photo');
                $input['imagename'] = uniqid() . '.' . $image->extension();

                $filePath = '/home/pcservi2/sspo/public_html/sspo/foto_socios/';
                //$image->move($filePath, $input['imagename']);
                //$nombreArchivo = uniqid() . '.' . $image->extension();

                $nombreArchivo = $usuario->id.'_'. Str::random(10). '.' . $image->extension();


                Storage::disk('foto_socios')->put($nombreArchivo, file_get_contents($image));

                // Actualizar la ruta de la imagen en la base de datos
                Socios::where('id', $usuarioId)->update(['photo_path' => $nombreArchivo]);
            }

            return response()->json(['tipo' => 'aprobado', 'icon' => 'success', 'title' => 'Imagen guardada', 'text' => 'La imagen ha sido guardada exitosamente.']);
        } catch (Exception $e) {
            //return response()->json([$e]);
            return response()->json(['tipo' => 'error', 'icon' => 'error', 'title' => 'Hubo un error durante el proceso.', 'text' => 'Por favor intente más tarde.']);
            $query = $e->getMessage();
        }
    }

    /*
    public function guardarFoto(Request $request)
    {
        try {
            // Obtener el valor de usuario_id desde la solicitud
            $usuarioId = $request->input('usuario_id');

            // Verificar si el usuario ya tiene una imagen almacenada
            $usuario = Socios::find($usuarioId);
            if (!empty($usuario->photo_path)) {
                // Si tiene una imagen almacenada, elimina la imagen anterior
                //Storage::disk('foto_socios')->delete($usuario->photo_path);
                File::delete(public_path('/foto_socios/' . $usuario->photo_path));
            }

            //if (File::exists($request->file('photo'))) {
            //    $image = $request->file('photo');
            //    $input['imagename'] = $usuario->id.'_'. Str::random(10). '.' . $image->extension();

            //    $filePath = public_path('/foto_socios/');
            //    $image->move($filePath, $input['imagename']);
            //    Socios::where('id', $usuarioId)->update(['photo_path' => $input['imagename']]);
            //}

            if (File::exists($request->file('photo'))) {
                $image = $request->file('photo');
                $input['imagename'] = uniqid() . '.' . $image->extension(); //time().'.'.$image->extension();

                $filePath = public_path('/foto_socios/');
                $image->move($filePath, $input['imagename']);

                // Actualizar la ruta de la imagen en la base de datos
                Socios::where('id', $usuarioId)->update(['photo_path' => $input['imagename']]);

            }


            return response()->json(['tipo' => 'aprobado', 'icon' => 'success', 'title' => 'Imagen guardada', 'text' => 'La imagen ha sido guardada exitosamente.']);
        } catch (Exception $e) {
            //return response()->json($e);
            return response()->json(['tipo' => 'error', 'icon' => 'error', 'title' => 'Hubo un error durante el proceso.', 'text' => 'Por favor intente más tarde.']);
            $query = $e->getMessage();
        }
    }*/

    public function ajaxSoccios(Request $request)
    {
        /*$draw = $request->get('draw');
        $start = (int)$request->get('start');
        $length = (int)$request->get('length');
        $search = $request->get('search.value');
        $totalMembers = Socios::count();
        $members = Socios::skip($start)->take($length)->get();

        $data = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalMembers,
            "iTotalDisplayRecords" => $totalMembers,
            "aaData" => $members
        ];

        return response()->json($data);*/


        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        //$searchValue = $search_arr['value']; // Search value

        if (!is_null($search_arr) && is_array($search_arr) && array_key_exists('value', $search_arr)) {
            $searchValue = $search_arr['value'];
        } else {
            // Manejar el caso en el que $search_arr no está definido o no contiene la clave 'value'
            $searchValue = ''; // O cualquier valor predeterminado que desees
        }

        // Total records
        $totalRecords =  Socios::count();

        $totalRecordswithFilter = Socios::where('socios.activo', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('nombre_completo', 'like', '%' . $searchValue . '%')
                    ->orWhere('apellido_paterno', 'like', '%' . $searchValue . '%')
                    ->orWhere('apellido_materno', 'like', '%' . $searchValue . '%')
                    ->orWhere('nombre', 'like', '%' . $searchValue . '%')
                    ->orWhere('cuip', 'like', '%' . $searchValue . '%')
                    ->orWhere('rfc', 'like', '%' . $searchValue . '%')
                    ->orWhere('tipo', 'like', '%' . $searchValue . '%');
            })
            ->count();

        // Fetch records
        $records = Socios::where('socios.activo', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('nombre_completo', 'like', '%' . $searchValue . '%')
                    ->orWhere('apellido_paterno', 'like', '%' . $searchValue . '%')
                    ->orWhere('apellido_materno', 'like', '%' . $searchValue . '%')
                    ->orWhere('nombre', 'like', '%' . $searchValue . '%')
                    ->orWhere('cuip', 'like', '%' . $searchValue . '%')
                    ->orWhere('rfc', 'like', '%' . $searchValue . '%')
                    ->orWhere('tipo', 'like', '%' . $searchValue . '%');
            })
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {

            $imgThum = '/foto_socios/' . $record->photo_path; //$record->photo_path;
            $defaultImg = '/foto_socios/socio.png'; // Ruta de la imagen default

            // Validar si la imagen existe
            if (!file_exists(public_path($imgThum))) {
                $imgThum = $defaultImg;
            }

            /* $imgThum = 'foto_socios/' . $record->photo_path; // Ruta relativa al sistema de archivos
            $defaultImg = 'https://theimgstudio.com/wp-content/uploads/2021/01/right-mobilesadf-asdfasfaRecovered-Recovered.png'; //'img/thumb/imgNoDisponible.jpg'; // Ruta de la imagen default

            // Validar si la imagen existe
            if (!Storage::disk('foto_socios')->exists($imgThum)) {
                $imgThum = $defaultImg;
            }*/

            $data_arr[] = array(
                'id' => $record->id,
                'imagen' => $imgThum,
                'nombre_completo' => $record->nombre_completo,
                'apellido_paterno' => $record->apellido_paterno,
                'apellido_materno' => $record->apellido_materno,
                'nombre' => $record->nombre,
                'saldo' => $record->saldo,
                'cuip' => $record->cuip,
                'rfc' => $record->rfc,
                'tipo' => $record->tipo,
                'is_fundador' => $record->is_fundador,
                'users_id' => $record->users_id,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );



        return response()->json($response);
    }

    public function cambioTipoSocio(Request $request)
    {
        try {
            // Obtener el valor de usuario_id desde la solicitud
            $usuarioId = $request->input('socios_id');
            Socios::where('id', $usuarioId)->update(['tipo_usuario' => $request->input('tipo_usuario')]);
            return response()->json(['tipo' => 'aprobado', 'icon' => 'success', 'title' => 'Cambio efectuado', 'text' => 'El tipo de usuario se ha cambiado.']);
        } catch (Exception $e) {
            //return response()->json($e);
            return response()->json(['tipo' => 'error', 'icon' => 'error', 'title' => 'Hubo un error durante el proceso.', 'text' => 'Por favor intente más tarde.']);
            $query = $e->getMessage();
        }
    }

    public function reciboSociosPrestamos($id)
    {
        // Obtener todos los préstamos de un socio
        $socio = Socios::findorfail($id);
        

        /*$prestamos = $socio->prestamos()
        ->where('debe', '>', 0)
        ->where('estatus', 'AUTORIZADO')
        ->orderBy('fecha_prestamo', 'asc')
        ->get();*/

        $prestamos = $socio->prestamos()
        ->where('debe', '>', 0)
        ->where('estatus', 'AUTORIZADO')
        ->with([
            'ultimoPagoPendiente',
            'ultimaSeriePagada',
        ])
        ->withSum(['pagos as capital_pendiente' => function ($query) {
            $query->where('pagado', 0);
        }], 'capital')
        ->orderBy('fecha_prestamo', 'asc')
        ->get();

        $prestamosDetalles = $socio->prestamoDetalles()
        ->where('debe', '>', 0)
        ->sum('debe');

        $totalCapitalPendiente = $prestamos->sum('capital_pendiente');

        //dd($prestamos, $prestamosDetalles, $totalCapitalPendiente);

        return response()->json(
            [
                'result' => 'success',
                'socio' => $socio,
                'prestamos' => $prestamos,
                'prestamo-detalle' => $prestamosDetalles,
                'total_capital_pendiente' => $totalCapitalPendiente,
            ]
        );
    }

    public function getSocioAjaxBySelect(Request $request)
    {
        $search = $request->input('search');

        // Realiza la consulta en función del término de búsqueda
        //$socios = Socios::where('activo', 1)
        //->when($search, function($query, $search) {
        //    return $query->where('nombre_completo', 'like', "%{$search}%");
        //})
        //->orderBy('nombre_completo', 'asc')
        //->limit(10)
        //->get();

        //$prestamosDetalles = $socios->prestamoDetalles()
        //->where('debe', '>', 0)
        //->sum('debe');

        /*$socios = Socios::withSum(['prestamoDetalles as prestamos_detalles_total' => function ($query) {
            $query->where('debe', '>', 0);
        }])
        ->where('activo', 1)
        ->when($search, function ($query, $search) {
            return $query->where('nombre_completo', 'like', "%{$search}%");
        })
        ->orderBy('nombre_completo', 'asc')
        ->limit(10)
        ->get();

        return response()->json($socios);*/

        try {
            $socios = Socios::withSum([
                'prestamoDetalles as prestamos_detalles_total' => function ($query) {
                    $query->where('debe', '>', 0);
                }
            ], 'debe')
                ->where('activo', 1)
                ->when($search, function ($query, $search) {
                    return $query->where('nombre_completo', 'like', "%{$search}%");
                })
                ->orderBy('nombre_completo', 'asc')
                ->limit(10)
                ->get();

            return response()->json($socios);

        } catch (\Exception $e) {
            //Log::error('Error al obtener socios: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    public function getSociossById(Request $request)
    {
        // Realiza la consulta en función del término de búsqueda
        $socios = Socios::findorfail($request->input('id'));

        return response()->json($socios);
    }

    public function ticketSaldo($id){

        $socio = Socios::findorfail($id);

        $prestamos = $socio->prestamos()
        ->where('debe', '>', 0)
        ->where('estatus', 'AUTORIZADO')
        ->with([
            'ultimoPagoPendiente',
            'ultimaSeriePagada',
        ])
        ->withSum(['pagos as capital_pendiente' => function ($query) {
            $query->where('pagado', 0);
        }], 'capital')
        ->orderBy('fecha_prestamo', 'asc')
        ->get();

        $prestamosDetalles = $socio->prestamoDetalles()
        ->where('debe', '>', 0)
        ->sum('debe');

        $totalCapitalPendiente = $prestamos->sum('capital_pendiente');

        //dd($prestamos);


        //  CREAMOS EL PDF  ----
        $userPrinterSize = 80;
        if ($userPrinterSize == '0'){
                return redirect('anticipo')
            ->with('status', 'La información ha sido guardada!');
        }
        $size = 0;
        if ($userPrinterSize == '58'){
            $size = array(0,0,140,1440);
        }
        if ($userPrinterSize == '80'){
            $size = array(0,0,212,1440);
        }

        $pdf = PDF::loadView('socios.partials.ticket_prestamos', compact('userPrinterSize','socio', 'prestamos', 'prestamosDetalles', 'totalCapitalPendiente'))
            ->setPaper($size,'portrait');
        return $pdf->stream();


    }
}
