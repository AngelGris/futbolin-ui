<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeepTrainningsToShoppingItems extends Migration
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
                    'name'          => 'Mantener entrenamientos',
                    'description'   => 'Mantener la racha de entrenamientos.',
                    'price'         => 1,
                    'icon'          => 'fa fa-star',
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
