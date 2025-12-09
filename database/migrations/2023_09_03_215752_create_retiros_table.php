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
        Schema::create('retiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socios_id')
                ->constrained('socios')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->dateTime('fecha_captura');
            $table->dateTime('fecha_retiro')->nullable();
            $table->decimal('monto_retiro', $precision = 12, $scale = 3);
            $table->decimal('saldo_aprobado', $precision = 12, $scale = 3);
            $table->decimal('saldo_actual', $precision = 12, $scale = 3)->default(0);
            $table->string('forma_pago',80)->nullable();
            $table->text('comentarios')->nullable();
            $table->string('estatus',40)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retiros');
    }
};
