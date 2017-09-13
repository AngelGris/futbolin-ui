<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function index(\App\Player $player)
    {
        $vars = [
            'icon' => 'fa fa-user',
            'title' => $player['first_name'] . ' ' . $player['last_name'],
            'subtitle' => 'Una parte del todo',
            'header_team' => $player['team'],
            'player' => $player
        ];

        return view('player.index', $vars);
    }

    public function showListing()
    {
        $vars = [
            'icon' => 'fa fa-group',
            'title' => 'Jugadores',
            'subtitle' => 'Los engranajes de la máquina',
            'players' => Auth::user()->team->players
        ];

        return view('player.listing', $vars);
    }
}
