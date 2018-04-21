<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Matches;
use App\Team;
use App\Player;
use App\TournamentCategory;
use Mail;

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
                'number' => $player['number'],
                'retiring' => $player['retiring'],
                'cards' => $player['cards']['cards'] >= \Config('constants.YELLOW_CARDS_SUSPENSION') - 1,
                'suspended' => $player['cards']['suspension'] > 0,
                'recovery' => $player['recovery'],
                'upgraded' => $player['upgraded'],
                'tired' => $player['tired'],
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

        $category_id =  \DB::table('tournament_categories')
                        ->join('tournament_positions', 'tournament_positions.category_id', '=', 'tournament_categories.id')
                        ->where('tournament_positions.team_id', '=', Auth::user()->team->id)
                        ->max('tournament_categories.id');

        if ($category_id) {
            $category = TournamentCategory::find($category_id);
            $tournament = $category->tournament;

            $next_match =   \DB::table('matches_rounds')
                            ->select('local_id', 'visit_id', 'datetime')
                            ->leftJoin('tournament_rounds', 'tournament_rounds.id', '=', 'matches_rounds.round_id')
                            ->where(function($query) use ($team) {
                                $query->where('local_id', '=', $team->id)
                                      ->orWhere('visit_id', '=', $team->id);
                            })->whereNull('match_id')
                            ->orderBy('number', 'ASC')
                            ->first();

            $last_matches =   \DB::table('matches_rounds')
                            ->select('match_id')
                            ->where(function($query) use ($team) {
                                $query->where('local_id', '=', $team->id)
                                      ->orWhere('visit_id', '=', $team->id);
                            })->whereNotNull('match_id')
                            ->orderBy('match_id', 'DESC')
                            ->limit(3)
                            ->get();
            $lm = [];
            foreach ($last_matches as $match) {
                $match = Matches::find($match->match_id);
                $lm[] = [
                    'date' => date('d/m/y', strtotime($match['created_at'])),
                    'local' => $match['local'],
                    'local_goals' => $match['local_goals'],
                    'visit' => $match['visit'],
                    'visit_goals' => $match['visit_goals'],
                    'logfile' => $match['logfile'],
                ];
            }

            $vars['tournament'] = [
                'category' => $category,
                'last_matches' => $lm,
            ];

            if ($next_match) {
                $vars['tournament']['next_match'] = [
                    'date' => date('d/m/y H:i', $next_match->datetime),
                    'local' => Team::find($next_match->local_id),
                    'visit' => Team::find($next_match->visit_id),
                ];
            }

        }

        return view('home', $vars);
    }
}
