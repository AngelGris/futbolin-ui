<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerSellingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_sellings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player_id');
            $table->unsignedInteger('value');
            $table->unsignedInteger('best_offer_value')->nullable();
            $table->unsignedInteger('best_offer_team')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->timestamps();

            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('best_offer_team')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_sellings');
    }
}
