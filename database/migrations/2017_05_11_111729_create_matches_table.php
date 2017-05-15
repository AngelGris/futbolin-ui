<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stadium');
            $table->integer('type_id')->unsigned()->default(0);
            $table->integer('local_id')->unsigned();
            $table->tinyInteger('local_goals')->unsigned();
            $table->integer('visit_id')->unsigned();
            $table->tinyInteger('visit_goals')->unsigned();
            $table->tinyInteger('winner')->unsigned();
            $table->string('logfile');
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('match_types');
            $table->foreign('local_id')->references('id')->on('teams');
            $table->foreign('visit_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
