<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShoppingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->tinyInteger('price')->unsigned();
            $table->string('icon');
            $table->boolean('in_shopping');
        });

        DB::table('shopping_items')->insert(
            [
                [
                    'name'          => 'In-filtrum',
                    'description'   => 'Recupera 20 puntos de energía de tus jugadores.',
                    'price'         => 1,
                    'icon'          => 'fa fa-heart-o',
                    'in_shopping'   => TRUE
                ],
                [
                    'name'          => 'In-filtrum Plus',
                    'description'   => 'Recupera TODA la energía de tus jugadores.',
                    'price'         => 3,
                    'icon'          => 'fa fa-heart',
                    'in_shopping'   => TRUE
                ],
                [
                    'name'          => 'Personal trainer',
                    'description'   => 'Entrena tu equipo automáticamente durante una semana.',
                    'price'         => 3,
                    'icon'          => 'fa fa-star-half-o',
                    'in_shopping'   => TRUE,
                ],
                [
                    'name'          => 'Terapia especializada',
                    'description'   => 'Ayuda a un jugador lesionado a recuperarse más rápido.',
                    'price'         => 1,
                    'icon'          => 'fa fa-medkit',
                    'in_shopping'   => FALSE
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopping_items');
    }
}
