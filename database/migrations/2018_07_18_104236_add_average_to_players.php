<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Player;

class AddAverageToPlayers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players', function(Blueprint $table) {
            $table->tinyInteger('average')->unsigned()->default(0)->after('healed');
        });

        $players = Player::withTrashed()->get();
        foreach ($players as $player) {
            $player->updateAverage();
        }

        DB::statement('UPDATE `player_sellings` SET `best_offer_value` = `value` WHERE `best_offer_value` IS NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['average']);
        });
    }
}
