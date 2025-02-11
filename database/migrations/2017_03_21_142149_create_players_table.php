<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->char('position', 3);
            $table->tinyInteger('age')->unsigned();
            $table->tinyInteger('goalkeeping')->unsigned();
            $table->tinyInteger('defending')->unsigned();
            $table->tinyInteger('dribbling')->unsigned();
            $table->tinyInteger('heading')->unsigned();
            $table->tinyInteger('jumping')->unsigned();
            $table->tinyInteger('passing')->unsigned();
            $table->tinyInteger('precision')->unsigned();
            $table->tinyInteger('speed')->unsigned();
            $table->tinyInteger('strength')->unsigned();
            $table->tinyInteger('tackling')->unsigned();
            $table->tinyInteger('number')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->foreign('team_id')->references('id')->on('teams');
            $table->timestamps();
        });

        /**
         * Create training teams
         */
        $strategies = DB::table('strategies')
            ->select('id', 'name')
            ->get();

        foreach ($strategies as $strategy) {
            DB::table('teams')->insert(
                [
                    'name' => 'Sparring 40 ' . $strategy->name,
                    'short_name' => 'SPA40',
                    'primary_color' => '#000000',
                    'secondary_color' => '#ffffff',
                    'text_color' => '#ffffff',
                    'stadium_name' => 'Campo de entrenamiento',
                    'strategy_id' => $strategy->id,
                    'formation' => '["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18"]',
                    'user_id' => 1
                ]
            );

            DB::table('teams')->insert(
                [
                    'name' => 'Sparring 60 ' . $strategy->name,
                    'short_name' => 'SPA60',
                    'primary_color' => '#000000',
                    'secondary_color' => '#ffffff',
                    'text_color' => '#ffffff',
                    'stadium_name' => 'Campo de entrenamiento',
                    'strategy_id' => $strategy->id,
                    'formation' => '["19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36"]',
                    'user_id' => 1
                ]
            );
        }

        $faker = Faker\Factory::create('es_AR');

        for ($i = 0; $i < 36; $i++) {
            if ($i < 18) {
                $age = 16;
                $value = 40;
            } else {
                $age = 17;
                $value = 60;
            }

            DB::table('players')->insert(
                [
                    'first_name' => $faker->firstName('male'),
                    'last_name' => $faker->lastName('male'),
                    'position' => 'SPA',
                    'age' => $age,
                    'goalkeeping' => $value,
                    'defending' => $value,
                    'dribbling' => $value,
                    'heading' => $value,
                    'jumping' => $value,
                    'passing' => $value,
                    'precision' => $value,
                    'speed' => $value,
                    'strength' => $value,
                    'tackling' => $value,
                    'number' => ($i % 18) + 1,
                    'team_id' => 1
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('players');
    }
}
