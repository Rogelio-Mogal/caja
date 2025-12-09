<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimuladorPrestamosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:saldo-simulador', ['only'=>['index']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/

    }

    public function index()
    {
        return view('simulador_prestamos.index');
    }
}
