<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MultiLanguageNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function(Blueprint $table) {
            $table->tinyInteger('notification_type')->unsigned()->default(0)->after('user_id');
            $table->string('variables', 255)->after('notification_type');
        });

        // Update notifications with variables
        $notifications = \App\Notification::where('notification_type', 0)->get();
        foreach ($notifications AS $notification) {
            if (preg_match('/^(.*) ha decidido dejar las canchas y (.*) ha sido incorporado al equipo.$/', $notification->getOriginal('message'), $matches)) {
                $notification->notification_type = \Config::get('constants.NOTIFICATIONS_PLAYER_RETIRED_AND_REPLACED');
                $notification->variables = ['player' => $matches[1], 'replacement' => $matches[2]];
            } elseif (preg_match('/^(.*) ha decidido dejar las canchas con (\d{2}) años.$/', $notification->getOriginal('message'), $matches)) {
                $notification->notification_type = \Config::get('constants.NOTIFICATIONS_PLAYER_RETIRED');
                $notification->variables = ['player' => $matches[1], 'age' => $matches[2]];
            } elseif (preg_match('/^(.*) hizo una mejor oferta por (.*), ¿vas a dejar que se lo queden\?$/', $notification->getOriginal('message'), $matches)) {
                $notification->notification_type = \Config::get('constants.NOTIFICATIONS_OFFER_EXCEEDED');
                $notification->variables = ['team_html' => $matches[1], 'player' => strip_tags($matches[2]), 'player_html' => $matches[2]];
            } elseif (preg_match('/^Has comprado a (.*) por (.*) y ya está a disposición del cuerpo técnico.$/', $notification->getOriginal('message'), $matches)) {
                $notification->notification_type = \Config::get('constants.NOTIFICATIONS_YOU_BOUGHT_PLAYER');
                $notification->variables = ['player' => strip_tags($matches[1]), 'player_html' => $matches[1], 'value' => $matches[2]];
            } elseif (preg_match('/^No has podido vender a (.*) y continua a disposición de tu cuerpo técnico.$/', $notification->getOriginal('message'), $matches)) {
                $notification->notification_type = \Config::get('constants.NOTIFICATIONS_NO_OFFER_FOR_PLAYER');
                $notification->variables = ['player' => strip_tags($matches[1]), 'player_html' => $matches[1]];
            } elseif (preg_match('/^(.*) ha sido transferido a (.*) por (.*).$/', $notification->getOriginal('message'), $matches)) {
                $notification->notification_type = \Config::get('constants.NOTIFICATIONS_PLAYER_TRANSFERRED');
                $notification->variables = ['player' => strip_tags($matches[1]), 'player_html' => $matches[1], 'buyer' => $matches[2], 'value' => $matches[3]];
            }

            $notification->save();
        }

        Schema::table('notifications', function (Blueprint $table) {
           $table->dropColumn(['title', 'message']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function(Blueprint $table) {
            $table->string('title');
            $table->text('message');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notification_type', 'variables']);
        });
    }
}
