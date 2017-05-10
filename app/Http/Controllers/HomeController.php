<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ages = [];
        for ($i = 0; $i < 10000; $i++) {
            $age = randomGauss(0, 20, 2);
            if (isset($ages[$age])) {
                $ages[$age]++;
            } else {
                $ages[$age] = 1;
            }
        }
        ksort($ages);

        $team = Auth::user()->team;

        if (is_null($team)) {
            return redirect('/equipo/crear');
        }

        $players = [];
        foreach ($team->players as $player) {
            $players[$player['id']] = $player;
        }

        $strategy = [];
        for ($i = 1; $i <= 11; $i++) {
            $strategy[$i - 1]['left'] = (int)($team->strategy->{sprintf('j%02d_start_y', $i)} * 1.11);
            $strategy[$i - 1]['top'] = (int)(100 - $team->strategy->{sprintf('j%02d_start_x', $i)} * 1.11);
        }

        $vars = [
            'icon' => 'fa fa-home',
            'title' => 'Vestuario',
            'subtitle' => 'Aquí comienza todo',
            'formation' => $team->formation,
            'players' => $players,
            'strategy' => $strategy,
        ];

        return view('home', $vars);
    }
}
