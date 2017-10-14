<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInjuriesToPlayers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('injuries', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->tinyInteger('recovery')->unsigned();
            $table->float('chance', 3, 2);
        });

        DB::table('injuries')->insert(
            [
                [
                    'name'      => 'Rotura de ligamentos cruzados',
                    'recovery'  => 14,
                    'chance'    => 0.04
                ],
                [
                    'name'      => 'Fractura de tibia y peroné',
                    'recovery'  => 12,
                    'chance'    => 0.07
                ],
                [
                    'name'      => 'Rotura de meniscos',
                    'recovery'  => 10,
                    'chance'    => 0.11
                ],
                [
                    'name'      => 'Pubalgia',
                    'recovery'  => 8,
                    'chance'    => 0.14
                ],
                [
                    'name'      => 'Rotura de falange del pie',
                    'recovery'  => 6,
                    'chance'    => 0.18
                ],
                [
                    'name'      => 'Esguince de tobillo',
                    'recovery'  => 4,
                    'chance'    => 0.21
                ],
                [
                    'name'      => 'Microdesgarro',
                    'recovery'  => 2,
                    'chance'    => 0.25
                ]
            ]
        );

        Schema::table('players', function(Blueprint $table) {
            $table->integer('injury_id')->unsigned()->nullable()->after('retiring');
            $table->tinyInteger('recovery')->unsigned()->default(0)->after('injury_id');

            $table->foreign('injury_id')->references('id')->on('injuries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['injury_id', 'recovery']);
        });

        Schema::dropIfExists('inuries');
    }
}
