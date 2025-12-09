<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AhorrosController;
use App\Http\Controllers\PrestamosController;
use App\Http\Controllers\SociosController;
use App\Http\Controllers\ExcelSocioController;
use App\Http\Controllers\ExcelAhorroController;
use App\Http\Controllers\RetiroController;
use App\Http\Controllers\SimuladorPrestamosController;
use App\Http\Controllers\PrestamosHistorialController;
use App\Http\Controllers\TesoreriaRetiroController;
use App\Http\Controllers\TesoreriaPrestamoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\AvalesController;
use App\Http\Controllers\DevolucionEfectivoController;
use App\Http\Controllers\EfectivoDiarioController;
use App\Http\Controllers\PagarPrestamoController;
use App\Http\Controllers\ReestructuracionController;
use App\Http\Controllers\PrestamosConceptosController;
use App\Http\Controllers\PrestamoEspecialController;
use App\Http\Controllers\PagosPrestamosController;
use App\Http\Controllers\PrestamoEnfermedadController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SectorCategoriaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'activo'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['activo'])->group(function () {
    Route::post('leer-excel-socios', [ExcelSocioController::class,'leerArchivoExcel'])
        ->name('leer.excel.socios');

    Route::post('leer-excel-ahorros', [ExcelAhorroController::class,'leerArchivoExcelAhorro'])
        ->name('leer.excel.ahorros');

    Route::post('obtiene-noDb-ahorros', [ExcelAhorroController::class,'excelNoDBAhorro'])
        ->name('obtener.noDb.ahorros');

    Route::post('obtiene-noFinanciero-ahorros', [ExcelAhorroController::class,'excelNoFinancieroAhorro'])
        ->name('obtener.noFinanciero.ahorros');

    Route::post('obtiene-socios-ahorros-excel', [ExcelAhorroController::class,'excelImportaAportacion'])
        ->name('obtener.socios.ahorros.excel');

    Route::post('detalle-socio-prestamo', [PrestamosController::class,'detalleSocio'])
        ->name('detalle.socio.prestamo');

    Route::post('detalle-aval-prestamo', [PrestamosController::class,'detalleAval'])
        ->name('detalle.aval.prestamo');

    Route::post('all-socios-prestamo', [PrestamosController::class,'allSocios'])
        ->name('all.socios.prestamo');

    Route::get('recibo-ahorro-voluntario/{id}', [AhorrosController::class,'reciboAhorroVoluntario'])
        ->name('recibo.ahorro.voluntario');

    Route::post('all-socios-ahorro', [AhorrosController::class,'allSocios'])
        ->name('all.socios.ahorro');

    Route::post('all-socios-retiro', [RetiroController::class,'allSocios'])
        ->name('all.socios.retiro');

    Route::get('recibo-retiro-aprobado/{id}', [TesoreriaRetiroController::class,'reciboRetiro'])
        ->name('recibo.retiro.aprobado');

    Route::get('recibo-prestamo-aprobado/{id}', [TesoreriaPrestamoController::class,'reciboPrestamo'])
        ->name('recibo.prestamo.aprobado');

    Route::post('guardar-foto', [SociosController::class,'guardarFoto'])
        ->name('guardar.foto');

    Route::get('ajax-socios',[SociosController::class,'ajaxSoccios'])->name('ajax.socios');

    Route::post('valida-aval', [PrestamosController::class,'validaAval'])
        ->name('valida.aval');

    Route::post('cambio-tipo-socio', [SociosController::class,'cambioTipoSocio'])
        ->name('cambio.tipo.socio');

    Route::get('recibo-reestructuracion-prestamo-pagado/{id}', [ReestructuracionController::class,'reciboRestructuracionPrestamoPagado'])
        ->name('recibo.reestructuracion.prestamo.pagado');

    Route::post('all-socios-prestamo-especial', [PrestamoEspecialController::class,'allSocios'])
        ->name('all.socios.prestamo.especial');

    Route::post('leer-excel-pagos', [PagosPrestamosController::class,'leerArchivoExcelPago'])
        ->name('leer.excel.pagos');

    Route::post('recibo-socios-prestamos/{id}', [SociosController::class,'reciboSociosPrestamos'])
        ->name('recibo.socios.prestamos');

    Route::get('usuarios',[UsuariosController::class,'GetUsers'])
        ->name('usuarios.all');

    Route::post('getSocioAjaxBySelect', [SociosController::class,'getSocioAjaxBySelect'])
        ->name('get.socios.ajax.by.select');

    Route::post('getSociossById', [SociosController::class,'getSociossById'])
        ->name('get.socios.by.id');

    Route::post('autoriza/prestamo', [PrestamosController::class,'autoriza3prestamo'])
        ->name('autoriza.tres.prestamo');

    Route::get('ajax-setor-categoria',[SectorCategoriaController::class,'ajaxSectorCategoria'])->name('ajax.sector.categoria');

    Route::get('solicitud-retiro/{id}', [RetiroController::class,'solicitudRetiro'])
        ->name('solicitud.retiro');

    Route::get('recibo-liquida-prestamo/{id}', [PagarPrestamoController::class,'reciboLiquidaPrestamo'])
        ->name('recibo.liquida.prestamo');

    Route::get('recibo-devolucion/{id}', [DevolucionEfectivoController::class,'reciboDevolucion'])
        ->name('recibo.devolucion');

    Route::get('reportes', [ReportesController::class,'index'])
        ->name('admin.reportes.index');

    Route::get('ticket-saldo/{id}', [SociosController::class, 'ticketSaldo'])
		->name('admin.ticket.saldo');

    Route::get('reportes/exportar-prestamo', [ReportesController::class, 'exportPrestamos'])->name('admin.reportes.exportar.prestamo');
    Route::get('reportes/exportar-prestamo-liquidacion', [ReportesController::class, 'exportLiquidosPrestamos'])->name('admin.reportes.exportar.prestamo.liquidacion');
    Route::get('reportes/exportar-retiros', [ReportesController::class, 'exportRetiros'])->name('admin.reportes.exportar.retiros');
    Route::get('reportes/exportar-ahorros', [ReportesController::class, 'exportAhorros'])->name('admin.reportes.exportar.ahorros');
    Route::get('reportes/exportar-historial', [HistorialController::class, 'exportHistorial'])->name('admin.reportes.exportar.historial');
    Route::get('reportes/exportar-cancela-prestamo-nomina', [ReportesController::class, 'exportCancelaPrestamoNomina'])->name('admin.reportes.exportar.cancela.prestamo.nomina');
    Route::get('reportes/exportar-ingresos-efectivo', [ReportesController::class, 'exportIngresosEfectivo'])->name('admin.reportes.ingresos.efectivo');
    Route::get('reportes/exportar-arqueo-caja', [ReportesController::class, 'exportArqueoCaja'])->name('admin.reportes.arqueo.caja');

    Route::resource('user',UserController::class)->names('admin.user');
    Route::resource('ahorros',AhorrosController::class)->names('admin.ahorros');
    Route::resource('prestamos',PrestamosController::class)->names('admin.prestamos');
    Route::resource('socios',SociosController::class)->names('admin.socios');
    Route::resource('excel-socios',ExcelSocioController::class)->names('admin.excel.socios');
    Route::resource('excel-ahorros',ExcelAhorroController::class)->names('admin.excel.ahorros');
    Route::resource('retiros',RetiroController::class)->names('admin.retiros');
    Route::resource('simulador-prestamos',SimuladorPrestamosController::class)->names('admin.simulador.prestamos');
    Route::resource('historial-prestamos',PrestamosHistorialController::class)->names('admin.historial.prestamos');
    Route::resource('tesoreria-retiro',TesoreriaRetiroController::class)->names('admin.tesoreria.retiro');
    Route::resource('tesoreria-prestamos',TesoreriaPrestamoController::class)->names('admin.tesoreria.prestamos');
    Route::resource('historial',HistorialController::class)->names('admin.historial');
    Route::resource('avales',AvalesController::class)->names('admin.avales');
    Route::resource('reestructuracion',ReestructuracionController::class)->names('admin.reestructuracion');
    Route::resource('prestamos-conceptos',PrestamosConceptosController::class)->names('admin.prestamos.comceptos');
    Route::resource('prestamos-especiales',PrestamoEspecialController::class)->names('admin.prestamos.especiales');
    Route::resource('pago-prestamos',PagosPrestamosController::class)->names('admin.pago.prestamos');
    Route::resource('prestamos-enfermedad',PrestamoEnfermedadController::class)->names('admin.prestamos.enfermedad');
    Route::resource('usuarios',UsuariosController::class)->names('admin.usuarios');
    Route::resource('roles',RolController::class)->names('admin.roles');
    Route::resource('pagar-prestamo',PagarPrestamoController::class)->names('admin.pagar.prestamo');
    Route::resource('sector-categoria',SectorCategoriaController::class)->names('admin.sector.categoria');
    Route::resource('devoluciones',DevolucionEfectivoController::class)->names('admin.devoluciones');
    Route::resource('efectivo-diario',EfectivoDiarioController::class)->names('admin.efectivo.diario');
});



