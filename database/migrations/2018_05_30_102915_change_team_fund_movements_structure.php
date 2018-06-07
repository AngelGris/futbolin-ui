<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTeamFundMovementsStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `team_fund_movements` MODIFY `created_at` TIMESTAMP NULL;');
        DB::statement('ALTER TABLE `players` MODIFY `team_id` int(10) UNSIGNED NULL;');
        Schema::table('team_fund_movements', function(Blueprint $table) {
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
        Schema::table('team_fund_movements', function(Blueprint $table) {
            $table->dropForeign('team_fund_movements_team_id_foreign');
        });
    }
}
