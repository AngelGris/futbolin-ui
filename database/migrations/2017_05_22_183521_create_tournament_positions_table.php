<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->tinyInteger('position')->unsigned();
            $table->tinyInteger('points')->unsigned()->default(0);
            $table->tinyInteger('played')->unsigned()->default(0);
            $table->tinyInteger('won')->unsigned()->default(0);
            $table->tinyInteger('tied')->unsigned()->default(0);
            $table->tinyInteger('lost')->unsigned()->default(0);
            $table->tinyInteger('goals_favor')->unsigned()->default(0);
            $table->tinyInteger('goals_against')->unsigned()->default(0);
            $table->tinyInteger('goals_difference')->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('tournament_categories');
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournament_positions');
    }
}
