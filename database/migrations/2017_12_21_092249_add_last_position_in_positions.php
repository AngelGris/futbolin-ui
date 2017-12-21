<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastPositionInPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournament_positions', function(Blueprint $table) {
            $table->tinyInteger('last_position')->unsigned()->default(0)->after('goals_difference');
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
            $table->dropColumn(['last_position']);
        });
    }
}
