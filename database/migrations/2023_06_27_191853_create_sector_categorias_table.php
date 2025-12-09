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
        Schema::create('sector_categorias', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['SECTOR','CATEGORÍA']);
            $table->string('nombre');
            $table->integer('wci');
            $table->boolean('activo')->default(1);
            $table->timestamps();

            // Definir la combinación única de nombre y tipo
            $table->unique(['nombre', 'tipo']);
        });

        DB::table("sector_categorias")
            ->insert([
                [
                    'tipo' => 'SECTOR',
                    'nombre' => '-',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'COMANDANCIA COSTA',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'COMANDANCIA ISTMO',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'COMANDANCIA MIXTECA',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'COMANDANCIA PAPALOAPAN',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'COMANDANCIA VALLES CENTRALES',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'D.R.A.I',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'G.O.E',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'GOBERNACION',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'INCAPACITADO',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'INVESTIGACION',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'MOTO PATRULLAS',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'REPUVE',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 1',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 2',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 3',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 4',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 5',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 6',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 7',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 8',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 9',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 10',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 11',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 12',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 13',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 14',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 15',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR 16',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'SECTOR PLAZA',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'U.P.O.E',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'UNIDAD CANINA',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'SECTOR',
                    'nombre' => 'UNIDAD TURISTICA',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => '-',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'INSPECTOR',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'OFICIAL',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'POLICIA "A"',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'POLICIA "B"',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'POLICIA 1/o.',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'POLICIA 2/o.',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'POLICIA 3/o.',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'POLICIA ESTATAL',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'SUBINSPECTOR',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
                [
                    'tipo' => 'CATEGORÍA',
                    'nombre' => 'SUBOFICIAL',
                    'wci' => 1,
                    'created_at'    =>  '2022-01-01 09:00:00',
                    'updated_at'    =>  '2022-01-01 09:00:00'
                ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sector_categorias');
    }
};
