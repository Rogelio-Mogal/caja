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
        Schema::create('socios_ajustes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socios_id')
                ->constrained('socios')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->string('tipo'); // ACTIVO, BAJA DE LA CAJA, etc.
            $table->date('fecha')->nullable(); // Fecha del cambio o ajuste
            $table->text('observaciones')->nullable();
            //$table->foreignId('usuario_id')->nullable()->constrained('users'); // quién hizo el ajuste
            $table->integer('wci');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socios_ajustes');
    }
};
