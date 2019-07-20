<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    /**
     * @param int $user_id
     * @param string $title
     * @param string $message
     * @param string $sound
     */
    public static function send(int $user_id, string $title, string $message, string $sound = 'default') {
        // Get all tokens for the user in the last month
        $tokens = ApiToken::where('user_id', $user_id)->where('used_on', '>', Carbon::now()->subMonth())->get();
        $token_array = [];
        foreach ($tokens as $token) {
            $token_array[] = $token->push_notification_token;
        }

        if (!empty($token_array)) {
            $push_notification = new \Edujugon\PushNotification\PushNotification();
            $push_notification->setService('fcm');
            $push_notification->setMessage([
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                    'sound' => $sound
                ],
                'data' => [
                    'extraPayLoad1' => 'value1',
                    'extraPayLoad2' => 'value2'
                ]
            ])
                ->setApiKey(\Config('pushnotification.fcm.apiKey'))
                ->setDevicesToken($token_array)
                ->send();
        }
    }
}
