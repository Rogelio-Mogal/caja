<?php

namespace App\Http\Controllers;

use App\Models\PrestamosConceptos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrestamosConceptosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:ver-concepto-prestamo-especial|crear-concepto-prestamo-especial|editar-concepto-prestamo-especial|borrar-concepto-prestamo-especial', ['only'=>['index']]);
        $this->middleware('permission:crear-concepto-prestamo-especial', ['only'=>['create', 'store']]);
        $this->middleware('permission:editar-concepto-prestamo-especial', ['only'=>['edit', 'update']]);
        $this->middleware('permission:borrar-concepto-prestamo-especial', ['only'=>['destroy']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        $prestamosConceptos = PrestamosConceptos::where('activo', 1)
            ->get();

        return view('prestamos_conceptos.index', compact('prestamosConceptos'));
    }

    public function create()
    {
        $prestamosConceptos = new PrestamosConceptos;

        return view('prestamos_conceptos.create', compact('prestamosConceptos'));
    }

    public function store(Request $request)
    {
        $prestamosConceptos = new PrestamosConceptos();

        $names  = array(
            'concepto' => 'CONCEPTO',
            'precio' => 'PRECIO',
            'num_plazos' => 'NUM PLAZOS',
            'num_piezas' => 'NUM PIEZAS',
        );

        $validator = Validator::make($request->all(), [
            'concepto' => 'required|string|min:2|max:250',
            'precio' => 'required|numeric|gt:0',
            'num_plazos' => 'required|numeric|gt:0',
            'num_piezas' => 'required|numeric|gt:0',
        ], [], $names);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $prestamosConceptos->concepto = strtoupper($request->input('concepto'));
            $prestamosConceptos->comentarios = strtoupper(nl2br($request->input('comentarios')));
            $prestamosConceptos->precio = $request->input('precio');
            $prestamosConceptos->num_plazos = $request->input('num_plazos');
            $prestamosConceptos->num_piezas = $request->input('num_piezas');
            $prestamosConceptos->disponibles = $request->input('num_piezas');
            $prestamosConceptos->save();
            $id = $prestamosConceptos->id;

            return redirect()->route('admin.prestamos.comceptos.index')->with(['id' => $id]);
        } catch (\Exception $e) {
            $query = $e->getMessage();
            return redirect()->back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show(PrestamosConceptos $prestamosConceptos)
    {
        //
    }

    public function edit(PrestamosConceptos $prestamosConceptos)
    {
        //
    }

    public function update(Request $request, PrestamosConceptos $prestamosConceptos)
    {
        //
    }

    public function destroy(PrestamosConceptos $prestamosConceptos)
    {
        //
    }
}
