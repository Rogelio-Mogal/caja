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
        Schema::create('efectivo_diarios', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha')->nullable();
            $table->decimal('b_mil', $precision = 12, $scale = 2);
            $table->decimal('b_quinientos', $precision = 12, $scale = 2);
            $table->decimal('b_doscientos', $precision = 12, $scale = 2);
            $table->decimal('b_cien', $precision = 12, $scale = 2);
            $table->decimal('b_cincuenta', $precision = 12, $scale = 2);
            $table->decimal('b_veinte', $precision = 12, $scale = 2);
            $table->decimal('monedas', $precision = 12, $scale = 2);
            $table->decimal('total', $precision = 12, $scale = 2);
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
        Schema::dropIfExists('efectivo_diarios');
    }
};
