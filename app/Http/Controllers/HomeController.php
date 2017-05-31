<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Matches;
use App\Team;
use App\Player;

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
        $team = Auth::user()->team;

        if (is_null($team)) {
            return redirect('/equipo/crear');
        }

        $strategy = [];
        for ($i = 1; $i <= 11; $i++) {
            $player = Player::find($team->formation[$i - 1]);

            $strategy[] = [
                'left' => (int)($team->strategy->{sprintf('j%02d_start_y', $i)} * 1.11),
                'top' => (int)(100 - $team->strategy->{sprintf('j%02d_start_x', $i)} * 1.11),
                'position' => $player['position'],
                'number' => $player['number']
            ];
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
                'local_id' => $match->local_id,
                'local' => $teams[$match->local_id]->short_name,
                'local_goals' => $match->local_goals,
                'visit_id' => $match->visit_id,
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
            'strategy' => $strategy,
            'overlay' => TRUE,
            'last_matches' => $last_matches
        ];

        return view('home', $vars);
    }
}
