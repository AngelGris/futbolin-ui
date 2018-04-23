<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('payment_status')->insert(
            [
                [
                    'name'  => 'Pending',
                ],
                [
                    'name'  => 'Accepted',
                ],
                [
                    'name'  => 'Declined',
                ],
            ]
        );

        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('credit_item_id')->unsigned();
            $table->char('method', 2);
            $table->float('amount_total', 4, 2)->unsigned();
            $table->float('amount_earnings', 4, 2)->unsigned();
            $table->integer('payment_status_id')->unsigned();
            $table->text('description');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('credit_item_id')->references('id')->on('credit_items');
            $table->foreign('payment_status_id')->references('id')->on('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_status');
    }
}
