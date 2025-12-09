<?php

namespace App\Http\Controllers;

use App\Models\SectorCategoria;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class SectorCategoriaController extends Controller
{

    public function index()
    {
        return view('sector_categoria.index');
    }

    public function create()
    {
        $sectorCategoria = new SectorCategoria;
        $tipoValues = ['SECTOR', 'CATEGORÍA'];

        return view('sector_categoria.create', compact('sectorCategoria', 'tipoValues'));
    }

    public function store(Request $request)
    {
        $sectorCategoria = new SectorCategoria();

        $names  = array(
            'nombre' => 'NOMBRE',
            'tipo' => 'SECTOR/CATEGORÍA',
        );

        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:50',
                Rule::unique('sector_categorias')->where(function ($query) use ($request) {
                    return $query->where('tipo', $request->tipo);
                }),
            ],
            'tipo' => 'required|in:SECTOR,CATEGORÍA',
        ], [], $names);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $sectorCategoria->nombre = $request->input('nombre');
            $sectorCategoria->tipo = $request->input('tipo');
            $sectorCategoria->wci = auth()->user()->id;
            $sectorCategoria->save();

            session()->flash('swal', [
                'icon' => "success",
                'title' => "Operación correcta",
                'text' => "El sector/categoría se creó correctamente.",
            ]);

            return redirect()->route('admin.sector.categoria.index');
        } catch (\Exception $e) {
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show($id)
    {
        $sectorCategoria = SectorCategoria::findorfail($id);

        return view('sector_categoria.delete', compact('sectorCategoria'));
    }

    public function edit($id)
    {
        $sectorCategoria = SectorCategoria::findorfail($id);
        $tipoValues = ['SECTOR', 'CATEGORÍA'];
        $metodo = 'edit';

        return view('sector_categoria.edit', compact('sectorCategoria','metodo','tipoValues'));
    }

    public function update(Request $request, $id)
    {
        $sectorCategoria = SectorCategoria::findorfail($id);

        $names  = array(
            'nombre' => 'NOMBRE',
            'tipo' => 'SECTOR/CATEGORÍA',
        );

        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:50',
                Rule::unique('sector_categorias')->ignore($id)->where(function ($query) use ($request) {
                    return $query->where('tipo', $request->tipo);
                }),
            ],
            'tipo' => 'required|in:SECTOR,CATEGORÍA',
        ], [], $names);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $sectorCategoria->update([
                'nombre' => $request->input('nombre'),
                'tipo' => $request->input('tipo'),
                'wci' => auth()->user()->id,
            ]);

            session()->flash('swal', [
                'icon' => "success",
                'title' => "Operación correcta",
                'text' => "Datos modificados correctamente.",
            ]);

            return redirect()->route('admin.sector.categoria.index');
        } catch (\Exception $e) {
            \DB::rollback();
            $query = $e->getMessage();
            /*return json_encode($query);*/
            return $query;
            return \Redirect::back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function destroy($id)
    {
        $sectorCategoria = SectorCategoria::findorfail($id);
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $sectorCategoria->update([
            'nombre' => $sectorCategoria->nombre . '-' . substr(str_shuffle($permitted_chars), 0, 5),
            'activo'  =>  0,
        ]);

        return response()->json([
            'swal' => [
                'icon' => "success",
                'title' => "Operación correcta",
                'text' => "El secto/categoría se ha sido eliminado correctamente.",
            ],
            'redirect' => route('admin.sector.categoria.index'),
        ]);
    }

    public function ajaxSectorCategoria(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        //$searchValue = $search_arr['value']; // Search value

        if (!is_null($search_arr) && is_array($search_arr) && array_key_exists('value', $search_arr)) {
            $searchValue = $search_arr['value'];
        } else {
            // Manejar el caso en el que $search_arr no está definido o no contiene la clave 'value'
            $searchValue = ''; // O cualquier valor predeterminado que desees
        }

        // Total records
        $totalRecords =  SectorCategoria::count();

        $totalRecordswithFilter = SectorCategoria::where('activo', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('tipo', 'like', '%' . $searchValue . '%')
                    ->orWhere('nombre', 'like', '%' . $searchValue . '%');
            })
            ->count();

        // Fetch records
        $records = SectorCategoria::where('activo', 1)
            ->where(function ($query) use ($searchValue) {
                $query->where('tipo', 'like', '%' . $searchValue . '%')
                    ->orWhere('nombre', 'like', '%' . $searchValue . '%');
            })
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {

            $data_arr[] = array(
                'id' => $record->id,
                'tipo' => $record->tipo,
                'nombre' => $record->nombre,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );



        return response()->json($response);
    }
}
