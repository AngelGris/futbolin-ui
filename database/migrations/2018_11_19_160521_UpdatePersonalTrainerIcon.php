<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePersonalTrainerIcon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('shopping_items')
            ->where('id', 3)
            ->update(['icon' => 'fa fa-user-circle']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('shopping_items')
            ->where('id', 3)
            ->update(['icon' => 'fa fa-star-half-o']
    );
    }
}
