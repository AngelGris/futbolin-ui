<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFreePlayerToShoppingItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('shopping_items')->insert(
            [
                [
                    'name'          => 'Liberar jugador',
                    'description'   => 'Rescindir contrato de un jugador.',
                    'price'         => 1,
                    'icon'          => 'fa fa-share-square',
                    'in_shopping'   => FALSE
                ]
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
        //
    }
}
