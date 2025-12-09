<?php

namespace App\Http\Controllers;

use App\Models\Tesoreria;
use Illuminate\Http\Request;

class TesoreriaController extends Controller
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

        return view('tesoreria.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Tesoreria $tesoreria)
    {
        //
    }

    public function edit(Tesoreria $tesoreria)
    {
        //
    }

    public function update(Request $request, Tesoreria $tesoreria)
    {
        //
    }

    public function destroy(Tesoreria $tesoreria)
    {
        //
    }
}
