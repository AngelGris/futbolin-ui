<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Player;

class PlayerController extends Controller
{
    public function index()
    {
    }

    public function injuries($domain, \App\Team $team)
    {
        return view('admin.player.injuries', ['players' => Player::where('recovery', '>', 0)->orderBy('recovery')->limit(10)->get()]);
    }
}
