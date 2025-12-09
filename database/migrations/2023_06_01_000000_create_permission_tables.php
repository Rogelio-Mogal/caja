<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id'); // permission id
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id'); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }

        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        // INSERCCIÓN DE DATOS

        // Agregar permisos
        DB::table($tableNames['permissions'])->insert([
            ['id' =>1, 'name' =>'ver-rol', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>2, 'name' =>'crear-rol', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>3, 'name' =>'editar-rol', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>4, 'name' =>'borrar-rol', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>5, 'name' =>'ver-perfil', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>6, 'name' =>'editar-perfil', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>7, 'name' =>'ver-socio', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>8, 'name' =>'ver-socio-historial', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>9, 'name' =>'crear-socio', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>10,'name' => 'editar-socio', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>11,'name' => 'borrar-socio', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>12,'name' => 'agregar-ahorro-voluntario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>13,'name' => 'agregar-ahorro-excel', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>14,'name' => 'aprobar-retiro', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>15,'name' => 'crear-prestamo', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>16,'name' => 'saldo-simulador', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>17,'name' => 'historial-prestamo', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>18,'name' => 'prestamos-diarios', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>19,'name' => 'ver-concepto-prestamo-especial', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>20,'name' => 'crear-concepto-prestamo-especial', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>21,'name' => 'editar-concepto-prestamo-especial', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>22,'name' => 'borrar-concepto-prestamo-especial', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>23,'name' => 'crear-reestructuracion', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>24,'name' => 'crear-prestamos-especiales', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>25,'name' => 'crear-prestamos-enfermedad', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>26,'name' => 'historial-avales', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>27,'name' => 'fianlizar-prestamo', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>28,'name' => 'finalizar-retiro', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>29,'name' => 'corte-caja', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>30,'name' => 'reposiscion-credencial', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>31,'name' => 'cargar-pago-prestamo-excel', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>32,'name' => 'historial-pago-prestamos', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>33,'name' => 'cargar-socios-excel', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>34,'name' => 'ver-usuario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>35,'name' => 'crear-usuario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>36,'name' => 'editar-usuario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>37,'name' => 'borrar-usuario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>38,'name' => 'pagar-prestamo', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' =>39,'name' => 'devoluciones', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Agregar roles
        DB::table($tableNames['roles'])->insert([
            ['id' => 1, 'name' => 'ADMINISTRADOR', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'FINANZAS', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Relacionar permisos con roles (role_has_permissions)
        DB::table($tableNames['role_has_permissions'])->insert([
            ['permission_id' => 1, 'role_id' => 1],
            ['permission_id' => 2, 'role_id' => 1],
            ['permission_id' => 3, 'role_id' => 1],
            ['permission_id' => 4, 'role_id' => 1],
            ['permission_id' => 5, 'role_id' => 1],
            ['permission_id' => 6, 'role_id' => 1],
            ['permission_id' => 7, 'role_id' => 1],
            ['permission_id' => 8, 'role_id' => 1],
            ['permission_id' => 9, 'role_id' => 1],
            ['permission_id' => 10, 'role_id' => 1],
            ['permission_id' => 11, 'role_id' => 1],
            ['permission_id' => 12, 'role_id' => 1],
            ['permission_id' => 13, 'role_id' => 1],
            ['permission_id' => 14, 'role_id' => 1],
            ['permission_id' => 15, 'role_id' => 1],
            ['permission_id' => 16, 'role_id' => 1],
            ['permission_id' => 17, 'role_id' => 1],
            ['permission_id' => 18, 'role_id' => 1],
            ['permission_id' => 19, 'role_id' => 1],
            ['permission_id' => 20, 'role_id' => 1],
            ['permission_id' => 21, 'role_id' => 1],
            ['permission_id' => 22, 'role_id' => 1],
            ['permission_id' => 23, 'role_id' => 1],
            ['permission_id' => 24, 'role_id' => 1],
            ['permission_id' => 25, 'role_id' => 1],
            ['permission_id' => 26, 'role_id' => 1],
            ['permission_id' => 27, 'role_id' => 1],
            ['permission_id' => 28, 'role_id' => 1],
            ['permission_id' => 29, 'role_id' => 1],
            ['permission_id' => 30, 'role_id' => 1],
            ['permission_id' => 31, 'role_id' => 1],
            ['permission_id' => 32, 'role_id' => 1],
            ['permission_id' => 33, 'role_id' => 1],
            ['permission_id' => 34, 'role_id' => 1],
            ['permission_id' => 35, 'role_id' => 1],
            ['permission_id' => 36, 'role_id' => 1],
            ['permission_id' => 37, 'role_id' => 1],
            ['permission_id' => 38, 'role_id' => 1],
            ['permission_id' => 39, 'role_id' => 1],
            ['permission_id' => 1, 'role_id' => 2],
            ['permission_id' => 5, 'role_id' => 2],
            ['permission_id' => 6, 'role_id' => 2],
            ['permission_id' => 7, 'role_id' => 2],
            ['permission_id' => 8, 'role_id' => 2],
            ['permission_id' => 9, 'role_id' => 2],
            ['permission_id' => 10, 'role_id' => 2],
            ['permission_id' => 12, 'role_id' => 2],
            ['permission_id' => 13, 'role_id' => 2],
            ['permission_id' => 14, 'role_id' => 2],
            ['permission_id' => 15, 'role_id' => 2],
            ['permission_id' => 16, 'role_id' => 2],
            ['permission_id' => 17, 'role_id' => 2],
            ['permission_id' => 18, 'role_id' => 2],
            ['permission_id' => 19, 'role_id' => 2],
            ['permission_id' => 20, 'role_id' => 2],
            ['permission_id' => 21, 'role_id' => 2],
            ['permission_id' => 22, 'role_id' => 2],
            ['permission_id' => 23, 'role_id' => 2],
            ['permission_id' => 24, 'role_id' => 2],
            ['permission_id' => 25, 'role_id' => 2],
            ['permission_id' => 26, 'role_id' => 2],
            ['permission_id' => 27, 'role_id' => 2],
            ['permission_id' => 28, 'role_id' => 2],
            ['permission_id' => 29, 'role_id' => 2],
            ['permission_id' => 30, 'role_id' => 2],
            ['permission_id' => 31, 'role_id' => 2],
            ['permission_id' => 32, 'role_id' => 2],
            ['permission_id' => 33, 'role_id' => 2],
        ]);

        // Crear usuario admin
        $userId = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin.mogal@gmail.com',
            'password' => Hash::make('madagascar'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Asignar rol al usuario
        DB::table('model_has_roles')->insert([
            'role_id' => 1,
            'model_type' => 'App\\Models\\User',
            'model_id' => $userId,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        // Borrar relaciones del usuario Admin
        DB::table('model_has_roles')->where('model_id', function ($query) {
            $query->select('id')->from('users')->where('email', 'admin.mogal@gmail.com');
        })->delete();

        // Borrar el usuario Admin
        DB::table('users')->where('email', 'admin.mogal@gmail.com')->delete();

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
};
