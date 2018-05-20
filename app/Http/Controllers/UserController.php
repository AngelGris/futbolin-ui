<?php

namespace App\Http\Controllers;

use App\User;
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
        if ($user->team) {
            $user->team->makeVisible(['last_trainning', 'trainer', 'trainning_count']);
            $formation = [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null];
            foreach($user->team->players as $player) {
                $pos = array_search($player->id, $user->team->formation);
                if ($pos !== FALSE) {
                    $formation[$pos] = $player;
                } else {
                    $formation[] = $player;
                }
            }
            $user->team->formation = $formation;
        }
        return response()->json([
            'user' => $user->makeVisible(['email', 'credits', 'last_activity'])
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
