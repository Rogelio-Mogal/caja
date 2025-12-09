<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Socios;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:ver-usuario|crear-usuario|editar-usuario|borrar-usuario', ['only'=>['index']]);
        $this->middleware('permission:crear-usuario', ['only'=>['create', 'store']]);
        $this->middleware('permission:editar-usuario', ['only'=>['edit', 'update']]);
        $this->middleware('permission:borrar-usuario', ['only'=>['destroy']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/

    }

    public function index()
    {
        $users = User::select('id','name','email','activo','created_at')       
            ->orderBy('name', 'desc')
            ->get();
        
            return view('users.index', compact('users'));
    }

    public function create()
    {
        $ids = request('id');
        $socios = Socios::findorfail($ids);
        $roles = Role::pluck('name','name')->all();

        return view('users.create', compact('socios','roles'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'name'  =>  'required|string|max:240|unique:users',
            'email' =>  'required|email|max:80|unique:users',
            'password'      =>  'required|min:10|max:20|confirmed',
        ]);

        try {
            \DB::beginTransaction();
            $fields = [
                'name' => request('name'),
                'email' => request('email'),
                'password' => bcrypt(request('password')),
            ];

            $usuario = User::create($fields);
            $id = $usuario->id;
            $usuario->assignRole($request->input('roles'));
            if($usuario){
                $socios = Socios::findorfail(request('socio_id'));
                $socios->update([
                    'users_id' => $usuario->id,
                    'tipo_usuario' => $request->input('roles'),
                ]);
            }
            // Le asignamos el rol de Cliente
            //$usuario->assignRole(request('rol'));

            \DB::commit();
            return redirect()->route('admin.usuarios.index')->with(['success' => $id, 'mensaje' =>'El Usuario ha sido creado.']);
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            /*return $query;*/
            return json_encode($query);
            return redirect::back()
                ->with(['error'=> 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput( $request->all(),$query );
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $user = User::find($id);
        $socio = Socios::where('users_id','=',$user->id)
        ->get()
        ->first();
        $perfil = 1;
        $roles = Role::pluck('name','name')->all();

        return view('usuarios.edit', compact('user','socio','perfil','roles'));
    }

    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $user = User::find($id);

            if ($user) {
                $user->update([
                    'name' => strtoupper($request->get('apellido_paterno')) . ' ' . strtoupper($request->get('apellido_materno')) . ' ' . strtoupper($request->get('nombre')),
                ]);

                Socios::where('users_id','=',$id)->update([
                    'apellido_paterno' =>  strtoupper($request->get('apellido_paterno')),
                    'apellido_materno' =>  strtoupper($request->get('apellido_materno')),
                    'nombre' =>  strtoupper($request->get('nombre')),
                    'nombre_completo' =>  strtoupper($request->get('apellido_paterno')) . ' ' . strtoupper($request->get('apellido_materno')). ' ' . strtoupper($request->get('nombre')),
                    'tipo_usuario' => $request->input('roles'),
                ]);

                // Elimina el id del rol
                DB::table('model_has_roles')
                ->where('model_id', $id)
                ->delete();
                $user->assignRole($request->input('roles'));
            }

            \DB::commit();
            return redirect()->route('admin.usuarios.edit', $id)->with(['success' => $id, 'mensaje' =>'El Perfil se ha actualizado.']);
        } catch (\Exception $e) {
            \DB::rollback();
            return \Redirect::back()
                ->with(['user' => 'fail', 'error' => $e->getMessage()])
                ->withInput( request()->all() );
        }
    }

    public function destroy($id)
    {
        User::find($id)->update([
            'activo' =>  0,
        ]);

        return redirect()->route('admin.usuarios.index');
    }
}
