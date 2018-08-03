<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastStatsToPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournament_positions', function(Blueprint $table) {
            $table->tinyInteger('last_points')->unsigned()->default(0)->after('last_position');
            $table->tinyInteger('last_goals_favor')->unsigned()->default(0)->after('last_points');
            $table->tinyInteger('last_goals_against')->unsigned()->default(0)->after('last_goals_favor');
            $table->tinyInteger('last_goals_difference')->default(0)->after('last_goals_against');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tournament_positions', function (Blueprint $table) {
            $table->dropColumn(['last_points', 'last_goals_favor', 'last_goals_against', 'last_goals_difference']);
        });
    }
}
