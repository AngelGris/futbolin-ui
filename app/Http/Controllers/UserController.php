<?php

namespace App\Http\Controllers;

use App\AdminMessage;
use App\TournamentRound;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Send loggedin User information through API
     */
    public function index()
    {
        $user = Auth::guard('api')->user()->user;
        $notifications = new \stdClass();
        if ($user->team) {
            $user->team->makeVisible(['last_trainning', 'trainer', 'trainning_count']);
            $user->team->append(['formation_objects']);

            $retiring = [];
            $players = $user->team->players->where('retiring', '=', 1);
            foreach ($players as $player) {
                $retiring[] = $player;
            }

            $last_match = TournamentRound::where('datetime', '<', time())->orderBy('datetime', 'DESC')->first();
            $upgraded = [];
            if ($last_match) {
                $players = $user->team->players->where('last_upgraded', '>', date('Y-m-d H:i:s', $last_match['datetime']))->sortByDesc('last_upgraded');
                foreach ($players as $player) {
                    $upgraded[] = $player;
                }
            }

            $request_time = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $notifications = [
                'unread'        => $user->unreadMessages,
                'messages'      => AdminMessage::where('valid_from', '<', $request_time)->where('valid_to', '>', $request_time)->orderBy('valid_from')->get(),
                'notifications' => $user->notifications->take(5),
                'transferables' => $user->team->players()->select('players.*', 'player_sellings.best_offer_value')->join('player_sellings', 'player_sellings.player_id', '=', 'players.id')->get(),
                'suspensions'   => $user->team->players()->select('players.*')->join('player_cards', 'player_cards.player_id', '=', 'players.id')->where('player_cards.suspension', '>', 0)->get(),
                'injuries'      => $user->team->players()->where('recovery', '>', 0)->get(),
                'retiring'      => $retiring,
                'upgraded'      => $upgraded
            ];
        }

        if (!is_null($user->team)) {
            $user->team->spending_margin = $user->team->calculateSpendingMargin();
        }

        return response()->json([
            'user' => $user->makeVisible(['email', 'credits', 'last_activity']),
            'notifications' => $notifications
        ], 200);
    }

    /**
     * Send User information through API
     */
    public function show(User $user)
    {
        return response()->json([
            'user' => $user
        ], 200);
    }
}
