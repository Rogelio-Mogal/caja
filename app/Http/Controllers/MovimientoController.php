<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

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
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Movimiento $movimiento)
    {
        //
    }

    public function edit(Movimiento $movimiento)
    {
        //
    }

    public function update(Request $request, Movimiento $movimiento)
    {
        //
    }

    public function destroy(Movimiento $movimiento)
    {
        //
    }
}
