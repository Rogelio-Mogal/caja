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
        Schema::create('devolucion_efectivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socios_id')
                ->constrained('socios')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->dateTime('fecha_captura');
            $table->dateTime('fecha_devolucion')->nullable();
            $table->decimal('importe', $precision = 12, $scale = 3);
            $table->string('forma_pago',150)->nullable();
            $table->string('referencia',150)->nullable();
            $table->text('nota')->nullable();
            $table->string('estatus')->nullable();
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
        Schema::dropIfExists('devolucion_efectivos');
    }
};
