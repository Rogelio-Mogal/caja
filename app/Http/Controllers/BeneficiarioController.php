<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use Illuminate\Http\Request;

class BeneficiarioController extends Controller
{
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
        $dataDetalle = array(
            'socios_id' => request('socios_id'),
            'nombre_beneficiario' => request('nombre_beneficiario'),
            'domicilio_beneficiario' => request('domicilio_beneficiario'),
            'telefono_beneficiario' => request('telefono_beneficiario'),
            'porcentaje_beneficiario' => request('porcentaje_beneficiario'),                      
        );
        //dd($dataDetalle);
        Beneficiario::create($dataDetalle);
        //dd($dataDetalle);

        /*$beneficiario = new Beneficiario();

        $beneficiario->socios_id = $request->input('socios_id');
        $beneficiario->nombre_beneficiario = $request->input('nombre_beneficiario');
        $beneficiario->domicilio_beneficiario = $request->input('domicilio_beneficiario');
        $beneficiario->telefono_beneficiario = $request->input('telefono_beneficiario');
        $beneficiario->porcentaje_beneficiario = $request->input('porcentaje_beneficiario');
        $beneficiario->save();*/
    }

    public function show(Beneficiario $beneficiario)
    {
        //
    }

    public function edit(Beneficiario $beneficiario)
    {
        //
    }

    public function update(Request $request, Beneficiario $beneficiario)
    {
        //
    }

    public function destroy(Beneficiario $beneficiario)
    {
        //
    }
}
