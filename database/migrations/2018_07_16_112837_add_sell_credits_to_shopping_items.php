<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSellCreditsToShoppingItems extends Migration
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
                    'name'          => 'Vender Fúlbos',
                    'description'   => 'Cambiar Fúlbos por dinero.',
                    'price'         => 1,
                    'icon'          => 'fa fa-money',
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
