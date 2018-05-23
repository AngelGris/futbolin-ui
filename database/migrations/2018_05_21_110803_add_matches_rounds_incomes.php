<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMatchesRoundsIncomes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matches_rounds', function(Blueprint $table) {
            $table->unsignedSmallInteger('assistance')->default(0)->after('visit_id');
            $table->unsignedMediumInteger('incomes')->default(0)->after('assistance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matches_rounds', function (Blueprint $table) {
            $table->dropColumn(['assistance', 'incomes']);
        });
    }
}
