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
        Schema::create('prestamos_conceptos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto', 255);
            $table->text('comentarios')->nullable();
            $table->decimal('precio', $precision = 12, $scale = 3);
            $table->integer('num_plazos');
            $table->integer('num_piezas');
            $table->integer('disponibles');
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos_conceptos');
    }
};
