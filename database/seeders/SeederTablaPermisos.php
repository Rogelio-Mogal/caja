<?php

namespace Database\Seeders;

//spatie
use Spatie\Permission\Models\Permission;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeederTablaPermisos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            //ROLES
            'ver-rol',
            'crear-rol',
            'editar-rol',
            'borrar-rol',
            //PERFIL
            'ver-perfil',
            'editar-perfil',
            //SOCIOS
            'ver-socio',
            'ver-socio-historial',
            'crear-socio',
            'editar-socio',
            'borrar-socio',
            //AHORRO
            'agregar-ahorro-voluntario',
            'agregar-ahorro-excel',
            'aprobar-retiro',
            //PRESTAMOS
            'crear-prestamo',
            'saldo-simulador',
            'historial-prestamo',
            'prestamos-diarios',
            //PRESTAMOS ESPECIALE
            'ver-concepto-prestamo-especial',
            'crear-concepto-prestamo-especial',
            'editar-concepto-prestamo-especial',
            'borrar-concepto-prestamo-especial',
            //REESTRUCTURACION
            'crear-reestructuracion',
            //PRESTAMOS ESPECIALES
            'crear-prestamos-especiales',
            'crear-prestamos-enfermedad',
            // HISTORIAL AVALES
            'historial-avales',
            //TESORERIA
            'fianlizar-prestamo',
            'finalizar-retiro',
            'corte-caja',
            'reposiscion-credencial',
            //PAGOS DE PRESTAMOS
            'cargar-pago-prestamo-excel',
            'historial-pago-prestamos',
            //CARGAR SOCIOS EXCEL
            'cargar-socios-excel',
            //ADMINISTRADOR
            'ver-usuario',
            'crear-usuario',
            'editar-usuario',
            'borrar-usuario',
        ];

        foreach($permisos as $permiso){
            Permission::create([
                'name' => $permiso
            ]);
        }
    }
}
