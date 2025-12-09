<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('socios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sector_id');
            $table->unsignedBigInteger('categoria_id');

            // Definir las relaciones con sector_categorias
            $table->foreign('sector_id')->references('id')->on('sector_categorias')->onDelete('no action');
            $table->foreign('categoria_id')->references('id')->on('sector_categorias')->onDelete('no action');

            $table->string('num_socio',40)->nullable();
            $table->string('photo_path',255)->nullable();
            $table->string('nombre',40);
            $table->string('apellido_paterno',40)->nullable();
            $table->string('apellido_materno',40)->nullable();
            $table->string('nombre_completo',255)->nullable();
            $table->string('rfc',25)->nullable();
            $table->dateTime('fecha_alta')->nullable();
            $table->string('telefono',80)->nullable();
            $table->string('domicilio',255)->nullable();
            $table->string('curp',25)->nullable();
            $table->string('cuip',35)->nullable();
            $table->string('estado_civil',30);
            $table->string('contacto_emergencia',255)->nullable();
            $table->string('telefono_emergencia',80)->nullable();
            $table->string('tipo_sangre',15)->nullable();
            $table->string('lugar_origen',100)->nullable();
            $table->date('alta_coorporacion')->nullable();
            $table->string('compania',80)->nullable();
            $table->string('batallon',80)->nullable();
            $table->decimal('saldo', $precision = 12, $scale = 3)->default(0);
            $table->decimal('monto_prestamos', $precision = 12, $scale = 3)->default(0);
            $table->decimal('debia', $precision = 12, $scale = 3)->default(0);
            $table->decimal('abona', $precision = 12, $scale = 3)->default(0);
            $table->decimal('debe', $precision = 12, $scale = 3)->default(0);
            $table->decimal('inscripcion', $precision = 12, $scale = 3)->default(0);
            $table->integer('numero_prestamos')->default(0);
            $table->integer('is_aval')->default(0);
            $table->integer('quincenas_inscrito')->default(0);
            $table->string('temporal_captura',40)->nullable();
            $table->string('tipo_usuario',20)->default('SOCIO'); // Socio, Ejecutivo, Administrador, Finanzas
            $table->integer('is_fundador')->default(0);
            $table->bigInteger('users_id')->default(0);
            $table->enum('tipo', ['ACTIVO','DEVOLUCIONES','BAJA VOLUNTARIA','FALLECIMIENTO','PENDIENTE','BAJA DE LA CORPORACIÓN','BAJA DE LA CAJA', 'TRÁMITE'])->default('ACTIVO');
            $table->date('fecha_baja')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamps();

            /*Create table movimientos(
                id_mov int auto_increment primary key, 
                numsocio varchar(60) not null,
                movimiento varchar(50) not null, 
                monto double not null, 
                metodopago varchar(80) not null,
                tipomov varchar(50) not null, 
                fechamov date not null,
                folio int not null
            );*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socios');
    }
};
