<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamsLastTrainning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->timestamp('last_trainning')->nullable()->after('formation');
            $table->integer('trainning_count')->default(0)->unsigned()->after('last_trainning');
        });

        Schema::table('players', function(Blueprint $table) {
            $table->timestamp('last_upgraded')->nullable()->after('last_upgrade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['last_trainning', 'trainning_count']);
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['last_upgraded']);
        });
    }
}
