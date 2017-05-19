<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Matches;
use App\Team;

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

        $matches = Matches::loadLastMatches($team->id);
        $last_matches = [];
        $teams = [$team->id => $team];
        foreach($matches as $match) {
            if (empty($teams[$match->local_id])) {
                $teams[$match->local_id] = Team::find($match->local_id);
            }
            if (empty($teams[$match->visit_id])) {
                $teams[$match->visit_id] = Team::find($match->visit_id);
            }

            $won = FALSE;
            if (($team['id'] == $match->local_id && $match->local_goals > $match->visit_goals) || ($team['id'] == $match->visit_id && $match->local_goals < $match->visit_goals)) {
                $won = TRUE;
            }

            $last_matches[] = [
                'date' => date('d/m/y', strtotime($match->created_at)),
                'local' => $teams[$match->local_id]->short_name,
                'local_goals' => $match->local_goals,
                'visit' => $teams[$match->visit_id]->short_name,
                'visit_goals' => $match->visit_goals,
                'log_file' => $match->logfile,
                'won' => $won,
            ];
        }

        $vars = [
            'icon' => 'fa fa-home',
            'title' => 'Vestuario',
            'subtitle' => 'Aquí comienza todo',
            'formation' => $team->formation,
            'players' => $players,
            'strategy' => $strategy,
            'last_matches' => $last_matches
        ];

        return view('home', $vars);
    }
}
