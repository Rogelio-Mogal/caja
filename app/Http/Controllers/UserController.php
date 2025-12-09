<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Socios;
use App\Models\User;
use DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:ver-perfil|editar-perfil', ['only'=>['index']]);
        $this->middleware('permission:editar-perfil', ['only'=>['edit', 'update']]);
    }
    
    // Función para obtener los datos del usuario desde la base de datos o alguna otra fuente
    private function obtener_usuario_actual()
    {
        $user = Auth::user();
        return $user;
    }

    public function index()
    {
        $user = Auth::user();
        $socio = Socios::where('users_id',$user->id)
        ->get()
        ->fisrt();
        $perfil = 0;
        

        return view('users.edit', compact('user','socio','perfil'));
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
        $user = Auth::user();
        $socio = Socios::where('users_id',$user->id)
        ->get()
        ->first();
        $perfil = 0;
        return view('users.edit', compact('user','socio','perfil'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $socio = Socios::where('users_id',$user->id)
        ->get()
        ->first();
        $perfil = 0;

        return view('users.edit', compact('user','socio','perfil'));
    }

    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            if( request('formulario') == 'user.informacion' ){
                // Obtener el usuario actual
                $user = $this->obtener_usuario_actual();
                $id = $user->id;

                $user = User::where('id', $user->id)->update([
                    'name' =>  strtoupper($request->get('apellido_paterno')) . ' ' . strtoupper($request->get('apellido_materno')). ' ' . strtoupper($request->get('nombre')),
                ]);


                Socios::where('users_id','=',$id)->update([
                    'apellido_paterno' =>  strtoupper($request->get('apellido_paterno')),
                    'apellido_materno' =>  strtoupper($request->get('apellido_materno')),
                    'nombre' =>  strtoupper($request->get('nombre')),
                    'nombre_completo' =>  strtoupper($request->get('apellido_paterno')) . ' ' . strtoupper($request->get('apellido_materno')). ' ' . strtoupper($request->get('nombre')),
                ]);

                \DB::commit();
                return redirect()->route('admin.user.show', $id)->with(['success' => $id, 'mensaje' =>'El Perfil se ha actualizado.']);
            }else if(request('formulario') == 'user.contrasena'){
                // Validar las entradas
                $request->validate([
                    'current_password' => ['required', function ($attribute, $value, $fail) {
                        if (!Hash::check($value, Auth::user()->password)) {
                            $fail('La contraseña actual no es correcta.');
                        }
                    }],
                    'new_password' => 'required|min:8',
                    'confirm_password' => ['required', function ($attribute, $value, $fail) use ($request) {
                        if ($value !== $request->input('new_password')) {
                            $fail('La confirmación de la nueva contraseña no coincide.');
                        }
                    }],
                ]);

                // Obtener el usuario actual
                $user = $this->obtener_usuario_actual();

                // Actualizar la contraseña
                $user->password = Hash::make($request->input('new_password'));
                $user->save();

                \DB::commit();
                //return \Redirect::back()->with('success', 'Contraseña actualizada exitosamente.');
                return \Redirect::back()->with(['success' => $id, 'mensaje' =>'El Perfil se ha actualizado.']);
            }


        } catch (\Exception $e) {
            \DB::rollback();
            return \Redirect::back()
                ->with(['user' => 'fail', 'error' => $e->getMessage()])
                ->withInput( request()->all() );
        }
    }

    public function destroy($id)
    {
        //
    }

}
