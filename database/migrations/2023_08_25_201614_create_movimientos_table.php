<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socios_id')
                ->constrained('socios')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->dateTime('fecha');
            $table->string('folio',255)->unique();
            $table->decimal('saldo_anterior', $precision = 12, $scale = 3)->default(0);
            $table->decimal('saldo_actual', $precision = 12, $scale = 3)->default(0);
            $table->decimal('monto', $precision = 12, $scale = 3);
            $table->string('movimiento',100);
            $table->string('tipo_movimiento',40);
            $table->string('metodo_pago',40);
            $table->nullableMorphs('origen');
            $table->string('estatus',40);
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('movimientos');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('movimientos');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
