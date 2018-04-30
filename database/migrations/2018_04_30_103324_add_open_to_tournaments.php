<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenToTournaments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournaments', function(Blueprint $table) {
            $table->boolean('closed')->default(FALSE)->after('zones');
        });

        \DB::table('tournaments')->where('id', '<', 5)->update(['closed' => TRUE]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['closed']);
        });
    }
}
