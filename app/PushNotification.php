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
    public static function send(int $user_id, string $title, string $message, array $data = [], string $sound = 'default') {
        // Get all tokens for the user in the last month
        $tokens = ApiToken::where('user_id', $user_id)->where('used_on', '>', Carbon::now()->subMonth())->get();
        $token_array = [];
        foreach ($tokens as $token) {
            $token_array[] = $token->push_notification_token;
        }

        if (!empty($token_array)) {
            // Filter unwanted $data
            $data_filtered = [];
            $data_valid = [
                'screen',
                'player_id',
                'tournament_id'
            ];
            foreach ($data as $key => $value) {
                if (in_array($key, $data_valid)) {
                    $data_filtered[$key] = $value;
                }
            }

            $push_notification = new \Edujugon\PushNotification\PushNotification();
            $push_notification->setService('fcm');
            $push_notification->setMessage([
                'notification' => [
                    'title' => strip_tags($title),
                    'body' => strip_tags($message),
                    'sound' => $sound
                ],
                'data' => $data_filtered,
            ])
                ->setApiKey(\Config('pushnotification.fcm.apiKey'))
                ->setDevicesToken($token_array)
                ->send();
        }
    }
}
