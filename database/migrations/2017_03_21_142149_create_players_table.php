<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->char('position', 3);
            $table->tinyInteger('goalkeeping')->unsigned();
            $table->tinyInteger('defending')->unsigned();
            $table->tinyInteger('dribbling')->unsigned();
            $table->tinyInteger('heading')->unsigned();
            $table->tinyInteger('jumping')->unsigned();
            $table->tinyInteger('passing')->unsigned();
            $table->tinyInteger('precision')->unsigned();
            $table->tinyInteger('speed')->unsigned();
            $table->tinyInteger('strength')->unsigned();
            $table->tinyInteger('tackling')->unsigned();
            $table->tinyInteger('number')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->foreign('team_id')->references('id')->on('teams');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('players');
    }
}
