<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->float('price', 4, 2)->unsigned();
            $table->tinyInteger('quantity')->unsigned();
        });

        DB::table('credit_items')->insert(
            [
                [
                    'name'          => 'Pack inferiores',
                    'description'   => 'Utiliza los Fúlbos para ayudar a tu equipo.',
                    'price'         => 1,
                    'quantity'      => 2
                ],
                [
                    'name'          => 'Pack reserva',
                    'description'   => 'Fúlbos con 20% de descuento.',
                    'price'         => 2,
                    'quantity'      => 5
                ],
                [
                    'name'          => 'Pack de primera',
                    'description'   => 'Fúlbos con 30% de descuento.',
                    'price'         => 3.5,
                    'quantity'      => 10
                ],
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
        Schema::dropIfExists('credit_items');
    }
}
