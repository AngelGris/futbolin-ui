<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->char('primary_color', 7);
            $table->char('secondary_color', 7);
            $table->string('stadium_name');
            $table->integer('strategy_id')->unsigned()->default(1);
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('strategy_id')->references('id')->on('strategies');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
