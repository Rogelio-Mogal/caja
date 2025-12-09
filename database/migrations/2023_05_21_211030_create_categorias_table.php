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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('categoria',200);
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        DB::table("categorias")
            ->insert([
            [
                "id" => 1,
                "categoria"       => "POLICIA ESTATAL",
            ],
            [
                "id" => 2,
                "categoria"       => "POLICIA PRIMERO",
            ],
            [
                "id" => 3,
                "categoria"       => "POLICIA SEGUNDO",
            ],
            [
                "id" => 4,
                "categoria"       => "POLICIA TERCERO",
            ],
            [
                "id" => 5,
                "categoria"       => "SUBOFICIAL",
            ],
            [
                "id" => 6,
                "categoria"       => "OFICIAL",
            ],
            [
                "id" => 7,
                "categoria"       => "SUBINSPECTOR",
            ],
            [
                "id" => 8,
                "categoria"       => "INSPECTOR",
            ],
            [
                "id" => 9,
                "categoria"       => "-",
            ],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
