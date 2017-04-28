<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
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
