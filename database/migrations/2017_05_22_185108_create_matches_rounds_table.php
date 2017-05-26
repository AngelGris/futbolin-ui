<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches_rounds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('round_id')->unsigned();
            $table->integer('local_id')->unsigned();
            $table->integer('visit_id')->unsigned();
            $table->integer('match_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('round_id')->references('id')->on('tournament_rounds');
            $table->foreign('local_id')->references('id')->on('teams');
            $table->foreign('visit_id')->references('id')->on('teams');
            $table->foreign('match_id')->references('id')->on('matches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches_rounds');
    }
}
