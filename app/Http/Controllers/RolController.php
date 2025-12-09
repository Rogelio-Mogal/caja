<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:ver-rol|crear-rol|editar-rol|borrar-rol', ['only'=>['index']]);
        $this->middleware('permission:crear-rol', ['only'=>['create', 'store']]);
        $this->middleware('permission:editar-rol', ['only'=>['edit', 'update']]);
        $this->middleware('permission:borrar-rol', ['only'=>['destroy']]);
    }

    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
       $permission = Permission::get();
       $role = new Role;
       $rolePermissions = [];
       return view('roles.create', compact('permission','role','rolePermissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required', 'permission'=> 'required'
        ]);
        try {
            \DB::beginTransaction();

            $role = Role::create([
                'name' => strtoupper($request->input('name')),
                'guard_name' => 'web'
            ]);
            $id = $role->id;

            $permissions = $request->input('permission'); // Array de IDs de permisos como cadenas
            $permissions = array_map('intval', $permissions); // Convertir las cadenas a enteros
            $role->syncPermissions($permissions); // Pasar el array de enteros

            \DB::commit();

            return redirect()->route('admin.roles.index')->with(['success' => $id, 'mensaje' =>'El Rol ha sido creado.']);
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')
        ->where('role_has_permissions.role_id', $id)
        ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
        ->all();

        return view('roles.edit', compact('role','permission','rolePermissions'));

    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => 'required', 'permission'=> 'required'
        ]);

        try {
            \DB::beginTransaction();

            $role = Role::find($id);
            $role->name = strtoupper($request->input('name'));
            $role->save();
            $id = $role->id;
            $permissions = $request->input('permission'); // Array de IDs de permisos como cadenas
            $permissions = array_map('intval', $permissions); // Convertir las cadenas a enteros
            $role->syncPermissions($permissions); // Pasar el array de enteros

            \DB::commit();
            return redirect()->route('admin.roles.index')->with(['success' => $id, 'mensaje' =>'El Rol ha sido editado.']);
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function destroy($id)
    {
        DB::table('roles')
        ->where('id', $id)
        ->delete();
        return redirect()->route('admin.roles.index');
    }
}
