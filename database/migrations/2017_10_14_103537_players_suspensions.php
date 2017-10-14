<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlayersSuspensions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suspensions', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('suspensions')->insert(
            [
                [
                    'name'      => 'Acumulación de amarillas',
                ],
                [
                    'name'      => 'Expulsión',
                ]
            ]
        );

        Schema::create('player_cards', function(Blueprint $table) {
            $table->integer('player_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->tinyInteger('cards')->unsigned()->default(0);
            $table->integer('suspension_id')->unsigned()->nullable();
            $table->tinyInteger('suspension')->unsigned()->default(0);

            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('category_id')->references('id')->on('tournament_categories');
            $table->unique(['player_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_cards');
        Schema::dropIfExists('suspensions');
    }
}
