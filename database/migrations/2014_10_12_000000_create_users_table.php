<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(FALSE);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert(
            [
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin',
                'password' => bcrypt('admin'),
                'is_admin' => TRUE
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
        Schema::dropIfExists('users');
    }
}
