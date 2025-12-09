<?php

namespace App\Http\Controllers;

use App\Models\Socios;
use App\Models\Prestamos;

use Illuminate\Http\Request;

class PrestamosHistorialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        //where('activo', 1)
        $socios = Socios::join('prestamos', 'socios.id', '=', 'prestamos.socios_id')
            ->where('estatus', '=', 'AUTORIZADO')
            ->select('socios.*')
            ->distinct()
            ->get();

        return view('historial_prestamos.index', compact('socios'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        // Obtener todos los préstamos de un socio
        $socio = Socios::findorfail($id);
        $prestamos = $socio->prestamos;

        return view('historial_prestamos.show', compact('prestamos', 'socio'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
