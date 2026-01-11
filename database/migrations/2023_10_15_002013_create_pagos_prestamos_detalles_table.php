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
        Schema::create('pagos_prestamos_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagos_prestamos_id')
                ->constrained('pagos_prestamos')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreignId('prestamos_id')
                ->constrained('prestamos')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreignId('socios_id')
                ->constrained('socios')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->string('tipo_cliente',50); //SOCIOS; AVAL
            $table->decimal('abona', $precision = 12, $scale = 3);

            $table->boolean('es_adelantado')->default(0);
            $table->boolean('es_reversion')->default(0);
            $table->unsignedBigInteger('reversion_de')->nullable();

            $table->integer('wci');
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_prestamos_detalles');
    }
};
