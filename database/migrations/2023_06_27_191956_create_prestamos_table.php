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
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socios_id')
                ->constrained('socios')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->dateTime('fecha_captura');
            $table->dateTime('fecha_prestamo')->nullable();
            $table->dateTime('fecha_ultimo_pago')->nullable();
            $table->date('fecha_ultimo_descuento')->nullable(); // es para colocar la fecha por el pago adelantado del prestamo
            $table->decimal('monto_prestamo', $precision = 12, $scale = 3);
            $table->decimal('total_intereses', $precision = 12, $scale = 3)->nullable();
            $table->decimal('pago_quincenal', $precision = 12, $scale = 3)->nullable();
            $table->decimal('total_quincenas', $precision = 12, $scale = 3);
            $table->decimal('debia', $precision = 12, $scale = 3)->default(0);
            $table->decimal('abona', $precision = 12, $scale = 3)->default(0);
            $table->decimal('debe', $precision = 12, $scale = 3)->default(0);
            $table->dateTime('fecha_pago_reestructuracion')->nullable();
            $table->decimal('monto_pago_reestructuracion')->nullable();
            $table->integer('serie')->default(0);
            $table->decimal('saldo_capital', $precision = 12, $scale = 3)->nullable();
            $table->decimal('saldo_interes', $precision = 12, $scale = 3)->nullable();
            $table->decimal('saldo_total', $precision = 12, $scale = 3)->nullable();
            $table->string('metodo_pago',40)->nullable();
            $table->decimal('diferencia', $precision = 12, $scale = 3)->nullable();
            $table->date('fecha_primer_pago')->nullable();
            $table->date('proximo_pago')->nullable();
            $table->string('folio',30)->nullable();
            $table->string('num_nomina',30)->nullable();
            $table->string('num_empleado',30)->nullable();
            $table->date('fecha_primer_corte')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->string('estatus',30)->nullable();
            $table->boolean('apoyo_adicional')->default(0);
            $table->boolean('prestamo_especial')->default(0);
            $table->boolean('prestamo_enfermedad')->default(0);
            $table->text('nota')->nullable();
            $table->boolean('compara_pago')->default(0);
            $table->json('documentacion')->nullable();
            $table->boolean('activo')->default(1);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
