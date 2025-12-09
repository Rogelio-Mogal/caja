<?php

namespace App\Http\Controllers;

use App\Models\EfectivoDiario;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

class EfectivoDiarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('permission:agregar-ahorro-voluntario', ['only'=>['index','create', 'store']]);

        /* $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create','store','storeProductoOnVenta');
        $this->middleware('can:admin.productos.edit')->only('edit','update');
        $this->middleware('can:admin.productos.destroy')->only('destroy');*/
    }

    public function index()
    {
        $efectivo = EfectivoDiario::where('activo',1)->get();

        return view('efectivo_diario.index', compact('efectivo'));
    }

    public function create()
    {
        $fechaHoy = now()->toDateString(); // o Carbon::now()->format('Y-m-d')

        // Verificar si ya existe un registro con esa fecha
        $yaExiste = EfectivoDiario::whereDate('fecha', $fechaHoy)
        ->where('activo', 1)
        ->exists();

        if ($yaExiste) {
            return redirect()->route('admin.efectivo.diario.index')
                ->withErrors(['fecha' => 'Ya existe un registro para el día de hoy.']);
        }

        $efectivo = new EfectivoDiario;
        return view('efectivo_diario.create', compact('efectivo'));
    }

    public function store(Request $request)
    {
        $fechaHoy = now()->toDateString();

        if (EfectivoDiario::whereDate('fecha', $fechaHoy)->where('activo', 1)->exists()) {
            return redirect()->back()
                ->withErrors(['fecha' => 'Ya se ha registrado un efectivo para hoy.'])
                ->withInput();
        }

        try {

            $denominaciones = [
                'b_mil' => 1000,
                'b_quinientos' => 500,
                'b_doscientos' => 200,
                'b_cien' => 100,
                'b_cincuenta' => 50,
                'b_veinte' => 20,
            ];

            $totalCalculado = 0;
            foreach ($denominaciones as $campo => $valor) {
                $cantidad = (int) $request->input($campo, 0);
                $totalCalculado += $cantidad * $valor;
            }

            $totalCalculado += (float) $request->input('monedas', 0);
            $totalIngresado = (float) $request->input('total');

            if ($totalCalculado !== $totalIngresado) {
                return redirect()->back()
                    ->withErrors(['total' => 'El total no coincide con la suma de las denominaciones.'])
                    ->withInput();
            }

            $efectivo = new EfectivoDiario();
            $efectivo->fecha = Carbon::now();

            $efectivo->b_mil       = (int) $request->input('b_mil', 0);
            $efectivo->b_quinientos = (int) $request->input('b_quinientos', 0);
            $efectivo->b_doscientos = (int) $request->input('b_doscientos', 0);
            $efectivo->b_cien       = (int) $request->input('b_cien', 0);
            $efectivo->b_cincuenta  = (int) $request->input('b_cincuenta', 0);
            $efectivo->b_veinte     = (int) $request->input('b_veinte', 0);
            $efectivo->monedas      = (float) $request->input('monedas', 0);

            $efectivo->total = $totalIngresado;
            $efectivo->wci = auth()->id();
            $efectivo->save();

            return redirect()->route('admin.efectivo.diario.index')->with(['id' => $efectivo->id]);
        } catch (Exception $e) {
            $query = $e->getMessage();
            // return json_encode($query);
            return redirect()->back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor intenetelo de nuevo.'])
                ->withInput($request->all(), $query);
        }
    }

    public function show(EfectivoDiario $efectivoDiario)
    {
        //
    }

    public function edit($id)
    {
        $efectivo = EfectivoDiario::findorfail($id);
        
        return view('efectivo_diario.edit', compact('efectivo'));
    }

    public function update(Request $request, $id)
    {
        try {
            $denominaciones = [
                'b_mil' => 1000,
                'b_quinientos' => 500,
                'b_doscientos' => 200,
                'b_cien' => 100,
                'b_cincuenta' => 50,
                'b_veinte' => 20,
            ];

            $totalCalculado = 0;
            foreach ($denominaciones as $campo => $valor) {
                $cantidad = (int) $request->input($campo, 0);
                $totalCalculado += $cantidad * $valor;
            }

            $totalCalculado += (float) $request->input('monedas', 0);
            $totalIngresado = (float) $request->input('total');

            if ($totalCalculado !== $totalIngresado) {
                return redirect()->back()
                    ->withErrors(['total' => 'El total no coincide con la suma de las denominaciones.'])
                    ->withInput();
            }

            $efectivo = EfectivoDiario::findOrFail($id);

            // Actualización
            $efectivo->b_mil       = (int) $request->input('b_mil', 0);
            $efectivo->b_quinientos = (int) $request->input('b_quinientos', 0);
            $efectivo->b_doscientos = (int) $request->input('b_doscientos', 0);
            $efectivo->b_cien       = (int) $request->input('b_cien', 0);
            $efectivo->b_cincuenta  = (int) $request->input('b_cincuenta', 0);
            $efectivo->b_veinte     = (int) $request->input('b_veinte', 0);
            $efectivo->monedas      = (float) $request->input('monedas', 0);

            $efectivo->total = $totalIngresado;
            $efectivo->wci = auth()->id();
            $efectivo->save();

            return redirect()->route('admin.efectivo.diario.index')->with(['id' => $efectivo->id]);
        } catch (Exception $e) {
            return redirect()->back()
                ->with(['error' => 'Hubo un error durante el proceso, por favor inténtelo de nuevo.'])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $efectivo = EfectivoDiario::findorfail($id);

            $efectivo->update([
                'activo' => 0
            ]);

            session()->flash('swal', [
                'icon' => "success",
                'title' => "Operación correcta",
                'text' => "Efectivo diario eliminado correctamente.",
                'customClass' => [
                    'confirmButton' => 'bg-red-500 text-white px-4 py-2 rounded'  // Aquí puedes añadir las clases CSS que quieras
                ],
            ]);
    
        } catch (\Exception $e) {
            session()->flash('swal', [
                'icon' => "error",
                'title' => "Operación fallida",
                'text' => "Hubo un error durante el proceso, por favor intente más tarde.",
                'customClass' => [
                    'confirmButton' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'  // Aquí puedes añadir las clases CSS que quieras
                ],
            ]);
            return redirect()->back()
                ->with('status', 'Hubo un error al ingresar los datos, por favor intente de nuevo.')
                ->withErrors(['error' => $e->getMessage()]); // Aquí pasas el mensaje de error
        }
    }
}
