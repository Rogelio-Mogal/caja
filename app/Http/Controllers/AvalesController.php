<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socios;
use App\Models\Prestamos;
use App\Models\PrestamoDetalle;

class AvalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:historial-avales', ['only'=>['index','show']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        $avales = PrestamoDetalle::join('prestamos', 'prestamos.id', '=', 'prestamo_detalles.prestamos_id')
        ->join('socios', 'socios.id', '=', 'prestamo_detalles.socios_id')
        ->select('socios.id','socios.nombre_completo','socios.cuip','socios.rfc')
        ->where('prestamos.estatus','=','AUTORIZADO')
        ->distinct()
        ->get();

        return view('avales.index', compact('avales'));

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
        $socio = Socios::with('prestamosAvalados.prestamo')->find($id);
        $prestamosAvalados = $socio->prestamosAvalados->pluck('prestamo')->unique();
        $montosAval = $socio->prestamoDetalles->pluck('monto_aval');
        //dd($prestamosAvalados);

        return view('avales.show', compact('prestamosAvalados', 'socio', 'montosAval'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
