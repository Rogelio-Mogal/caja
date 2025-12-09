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
        Schema::create('datos_temporales', function (Blueprint $table) {
            $table->id();
            $table->string('sector_id',100)->nullable();
            $table->string('categoria_id',100)->nullable();
            $table->string('num_socio',40)->nullable();
            $table->string('nombre',40)->nullable();
            $table->string('apellido_paterno',40)->nullable();
            $table->string('apellido_materno',40)->nullable();
            $table->string('nombre_completo',255)->nullable();
            $table->string('rfc',25)->nullable();
            $table->date('fecha_alta')->nullable();
            $table->string('telefono',80)->nullable();
            $table->string('domicilio',255)->nullable();
            $table->string('curp',25)->nullable();
            $table->string('cuip',35)->nullable();
            $table->string('estado_civil',30)->nullable();
            $table->string('contacto_emergencia',255)->nullable();
            $table->string('telefono_emergencia',80)->nullable();
            $table->string('tipo_sangre',15)->nullable();
            $table->string('lugar_origen',100)->nullable();
            $table->date('alta_coorporacion')->nullable();
            $table->string('compania',80)->nullable();
            $table->string('batallon',80)->nullable();
            $table->string('temporal_captura',40)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_temporales');
    }
};
