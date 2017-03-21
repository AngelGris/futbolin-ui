<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strategies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->float('j01_start_x', 5, 2);
            $table->float('j01_start_y', 5, 2);
            $table->float('j01_end_x', 5, 2);
            $table->float('j01_end_y', 5, 2);
            $table->float('j02_start_x', 5, 2);
            $table->float('j02_start_y', 5, 2);
            $table->float('j02_end_x', 5, 2);
            $table->float('j02_end_y', 5, 2);
            $table->float('j03_start_x', 5, 2);
            $table->float('j03_start_y', 5, 2);
            $table->float('j03_end_x', 5, 2);
            $table->float('j03_end_y', 5, 2);
            $table->float('j04_start_x', 5, 2);
            $table->float('j04_start_y', 5, 2);
            $table->float('j04_end_x', 5, 2);
            $table->float('j04_end_y', 5, 2);
            $table->float('j05_start_x', 5, 2);
            $table->float('j05_start_y', 5, 2);
            $table->float('j05_end_x', 5, 2);
            $table->float('j05_end_y', 5, 2);
            $table->float('j06_start_x', 5, 2);
            $table->float('j06_start_y', 5, 2);
            $table->float('j06_end_x', 5, 2);
            $table->float('j06_end_y', 5, 2);
            $table->float('j07_start_x', 5, 2);
            $table->float('j07_start_y', 5, 2);
            $table->float('j07_end_x', 5, 2);
            $table->float('j07_end_y', 5, 2);
            $table->float('j08_start_x', 5, 2);
            $table->float('j08_start_y', 5, 2);
            $table->float('j08_end_x', 5, 2);
            $table->float('j08_end_y', 5, 2);
            $table->float('j09_start_x', 5, 2);
            $table->float('j09_start_y', 5, 2);
            $table->float('j09_end_x', 5, 2);
            $table->float('j09_end_y', 5, 2);
            $table->float('j10_start_x', 5, 2);
            $table->float('j10_start_y', 5, 2);
            $table->float('j10_end_x', 5, 2);
            $table->float('j10_end_y', 5, 2);
            $table->float('j11_start_x', 5, 2);
            $table->float('j11_start_y', 5, 2);
            $table->float('j11_end_x', 5, 2);
            $table->float('j11_end_y', 5, 2);
            $table->timestamps();
        });

        DB::table('strategies')->insert(
            [
                'name' => '4-4-2',
                'j01_start_x' => 45.0,
                'j01_start_y' => 0.5,
                'j01_end_x' => 45.0,
                'j01_end_y' => 25.0,
                'j02_start_x' => 35.0,
                'j02_start_y' => 10.5,
                'j02_end_x' => 35.0,
                'j02_end_y' => 45.0,
                'j03_start_x' => 15.0,
                'j03_start_y' => 25.0,
                'j03_end_x' => 15.0,
                'j03_end_y' => 55.0,
                'j04_start_x' => 75.0,
                'j04_start_y' => 25.0,
                'j04_end_x' => 75.0,
                'j04_end_y' => 55.0,
                'j05_start_x' => 35.0,
                'j05_start_y' => 35.0,
                'j05_end_x' => 35.0,
                'j05_end_y' => 75.0,
                'j06_start_x' => 55.0,
                'j06_start_y' => 10.5,
                'j06_end_x' => 55.0,
                'j06_end_y' => 45.0,
                'j07_start_x' => 15.0,
                'j07_start_y' => 45.0,
                'j07_end_x' => 15.0,
                'j07_end_y' => 95.0,
                'j08_start_x' => 75.0,
                'j08_start_y' => 45.0,
                'j08_end_x' => 75.0,
                'j08_end_y' => 95.0,
                'j09_start_x' => 35.0,
                'j09_start_y' => 55.0,
                'j09_end_x' => 35.0,
                'j09_end_y' => 105.0,
                'j10_start_x' => 55.0,
                'j10_start_y' => 35.0,
                'j10_end_x' => 55.0,
                'j10_end_y' => 75.0,
                'j11_start_x' => 55.0,
                'j11_start_y' => 55.0,
                'j11_end_x' => 55.0,
                'j11_end_y' => 105.0,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strategies');
    }
}
