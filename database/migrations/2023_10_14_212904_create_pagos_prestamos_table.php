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
        Schema::create('pagos_prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamos_id')
                ->constrained('prestamos')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreignId('socios_id')
                ->constrained('socios')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->dateTime('fecha_pago')->nullable();
            $table->dateTime('fecha_captura')->nullable();
            $table->integer('serie_pago');
            $table->integer('serie_final');
            $table->decimal('importe', $precision = 12, $scale = 3);
            $table->string('forma_pago',150)->nullable();
            $table->string('metodo_pago',150)->nullable();
            $table->string('referencia',150)->nullable();

            $table->decimal('capital', $precision = 12, $scale = 2);
            $table->decimal('interes', $precision = 12, $scale = 2);
            $table->decimal('decuento', $precision = 12, $scale = 2);
            $table->date('fecha_tabla')->nullable(); // Fecha en la cual se realiza el descuento --de la tabla
            $table->boolean('pagado')->default(0);

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
        Schema::dropIfExists('pagos_prestamos');
    }
};
