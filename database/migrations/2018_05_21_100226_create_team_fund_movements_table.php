<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamFundMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_fund_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id');
            $table->integer('amount');
            $table->integer('balance');
            $table->string('description');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_fund_movements');
    }
}
