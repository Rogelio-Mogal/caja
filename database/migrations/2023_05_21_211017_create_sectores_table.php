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
        Schema::create('sectores', function (Blueprint $table) {
            $table->id();
            $table->string('sector',200);
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        DB::table("sectores")
            ->insert([
            [
                "id"    => 1, 
                "sector"       => "PLAZA",
            ],
            [
                "id"    => 2, 
                "sector"       => "1RO SECTOR HUAJUAPAN",
            ],
            [
                "id"    => 3, 
                "sector"       => "2DO SECTOR CUICATLAN",
            ],
            [
                "id"    => 4, 
                "sector"       => "3ER SECTOR TUXTEPEC",
            ],
            [
                "id"    => 5, 
                "sector"       => "4TO SECTOR CAMARON",
            ],
            [
                "id"    => 6, 
                "sector"       => "5TO SECTOR PALOMARES",
            ],
            [
                "id"    => 7, 
                "sector"       => "6TO SECTOR JUCHITAN",
            ],
            [
                "id"    => 8, 
                "sector"       => "7MO SECTOR HUATULCO",
            ],
            [
                "id"    => 9, 
                "sector"       => "8VO SECTOR PUERTO ESCONDIDO",
            ],
            [
                "id"    => 10, 
                "sector"       => "9NO SECTOR PINOTEPA NACIONAL",
            ],
            [
                "id"    => 11, 
                "sector"       => "10 SECTOR PUTLA DE GUERRERO",
            ],
            [
                "id"    => 12, 
                "sector"       => "11 SECTOR TEPOSCOLULA",
            ],
            [
                "id"    => 13, 
                "sector"       => "12 SECTOR MIAHUATLAN",
            ],
            [
                "id"    => 14, 
                "sector"       => "13SECTOR TANIVET",
            ],
            [
                "id"    => 15, 
                "sector"       => "14 SECTOR JUXTLAHUACA",
            ],
            [
                "id"    => 16, 
                "sector"       => "15 SECTOR SOLA DE VEGA",
            ],
            [
                "id"    => 17,
                "sector"       => "-",
            ],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sectores');
    }
};
