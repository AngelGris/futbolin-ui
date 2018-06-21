<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player_id');
            $table->unsignedInteger('seller_id')->nullable();
            $table->unsignedInteger('buyer_id');
            $table->unsignedInteger('value');
            $table->timestamp('created_at')->nullable();

            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('seller_id')->references('id')->on('teams');
            $table->foreign('buyer_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_transactions');
    }
}
