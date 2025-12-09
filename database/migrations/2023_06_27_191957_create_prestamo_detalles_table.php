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
        Schema::create('prestamo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamos_id')
                ->constrained('prestamos')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreignId('socios_id')
                ->constrained('socios')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->string('aval',255);
            $table->string('num_aval',255)->default(0);
            $table->decimal('monto_socio', $precision = 12, $scale = 3);
            $table->decimal('monto_aval', $precision = 12, $scale = 3);
            $table->dateTime('fecha_ultimo_pago')->nullable();
            $table->decimal('debia', $precision = 12, $scale = 3)->default(0);
            $table->decimal('abona', $precision = 12, $scale = 3)->default(0);
            $table->decimal('debe', $precision = 12, $scale = 3)->default(0);
            $table->dateTime('fecha_pago_reestructuracion')->nullable();
            $table->decimal('monto_pago_reestructuracion')->nullable();
            $table->string('num_nomina',30)->nullable();
            $table->string('num_empleado',30)->nullable();
            $table->boolean('apoyo_adicional')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamo_detalles');
    }
};
