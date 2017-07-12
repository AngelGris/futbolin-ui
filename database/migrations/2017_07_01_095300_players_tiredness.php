<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Player;

class PlayersTiredness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players', function(Blueprint $table) {
            $table->tinyInteger('condition')->unsigned()->default(0)->after('tackling');
            $table->tinyInteger('stamina')->unsigned()->default(100)->after('condition');
        });

        $sparrings = Player::where('id', '<=', 36)->get();
        foreach ($sparrings as $sparring) {
            if ($sparring->id <= 18) {
                $sparring->condition = 80;
            } else{
                $sparring->condition = 90;
            }
            $sparring->save();
        }

        $players = Player::where('id', '>', 36)->get();
        foreach ($players as $player) {
            $player->condition = randomGauss(80, 100, 10);
            $player->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['condition', 'stamina']);
        });
    }
}
