<?php

namespace App\Http\Controllers;

use App\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function index(Player $player, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'player'    => $player
            ], 200);
        } else {
            $vars = [
                'icon' => 'fa fa-user',
                'title' => $player['first_name'] . ' ' . $player['last_name'],
                'subtitle' => 'Una parte del todo',
                'header_team' => $player['team'],
                'player' => $player
            ];

            return view('player.index', $vars);
        }
    }

    public function showListing(Request $request)
    {
        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
            return response()->json([
                'players'   => (is_null($user->team) ? null : $user->team->players)
            ], 200);
        } else {
            $vars = [
                'icon'      => 'fa fa-group',
                'title'     => 'Jugadores',
                'subtitle'  => 'Los engranajes de la máquina',
                'players'   => Auth::user()->team->players
            ];

            return view('player.listing', $vars);
        }
    }
}
