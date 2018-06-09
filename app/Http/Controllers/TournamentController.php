<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Matches;
use App\Team;
use App\Tournament;
use App\TournamentCategory;

class TournamentController extends Controller
{
    public function index(Request $request, $category_id = NULL)
    {
        $vars = [
            'icon' => 'fa fa-trophy',
            'title' => 'Torneos',
            'subtitle' => 'A demostrar quién manda'
        ];

        if ($request->expectsJson()) {
            $team = Auth::guard('api')->user()->user->team;
        } else {
            $team = Auth::user()->team;
        }

        if (is_null($category_id)) {
            $category_id =  \DB::table('tournament_categories')
                            ->join('tournament_positions', 'tournament_positions.category_id', '=', 'tournament_categories.id')
                            ->where('tournament_positions.team_id', '=', $team->id)
                            ->max('tournament_categories.id');
        }

        if ($category_id) {
            if (!$category = TournamentCategory::find($category_id)) {
                return redirect()->route('tournaments');
            }

            $tournament = $category->tournament;

            $last_round =   \DB::table('tournament_rounds')
                            ->select('number')
                            ->where('category_id', '=', $category_id)
                            ->where('datetime', '<', $_SERVER['REQUEST_TIME'])
                            ->orderBy('datetime', 'DESC')
                            ->first();

            $vars['tournament'] = $tournament;
            $vars['category'] = $category;
            $vars['last_round'] = $last_round ? $last_round->number : 1;
        } else {
            $tournament = Tournament::latest()->first();

            if ($tournament) {
                $category = $tournament->tournamentCategories()->oldest()->first();

                $last_round =   \DB::table('tournament_rounds')
                                ->select('number')
                                ->where('category_id', '=', $category_id)
                                ->where('datetime', '<', $_SERVER['REQUEST_TIME'])
                                ->orderBy('datetime', 'DESC')
                                ->first();

                $vars['tournament'] = $tournament;
                $vars['category'] = $category;
                $vars['last_round'] = $last_round ? $last_round->number : 1;
            }
        }

        $vars['categories'] = TournamentCategory::where('tournament_id', '=', $tournament->id)->get();

        if ($request->expectsJson()) {
            $result =   \DB::table('matches_rounds')
                        ->select('local_id', 'visit_id', 'datetime')
                        ->leftJoin('tournament_rounds', 'tournament_rounds.id', '=', 'matches_rounds.round_id')
                        ->where(function($query) use ($team) {
                            $query->where('local_id', '=', $team->id)
                                  ->orWhere('visit_id', '=', $team->id);
                        })->whereNull('match_id')
                        ->orderBy('number', 'ASC')
                        ->first();
            $next_match = [];
            if ($result) {
                $local = Team::find($result->local_id);
                $visit = Team::find($result->visit_id);
                $next_match = [
                    'datetime'  => $result->datetime,
                    'stadium'   => $local->stadium_name,
                    'local'     => $local,
                    'visit'     => $visit
                ];
            }

            $result =   \DB::table('matches_rounds')
                        ->select('match_id')
                        ->where(function($query) use ($team) {
                            $query->where('local_id', '=', $team->id)
                                  ->orWhere('visit_id', '=', $team->id);
                        })->whereNotNull('match_id')
                        ->orderBy('match_id', 'DESC')
                        ->limit(3)
                        ->get();
            $last_matches = [];
            foreach ($result as $match) {
                $match = Matches::find($match->match_id);
                $last_matches[] = [
                    'datetime'  => $match->created_at->timestamp,
                    'stadium'   => $match->stadium,
                    'local'     => $match->local,
                    'visit'     => $match->visit
                ];
            }

            return response()->json([
                'tournament'    => $tournament,
                'category'      => $category,
                'next_match'    => $next_match,
                'last_matches'  => $last_matches
            ], 200);
        } else {
            return view('tournament.index', $vars);
        }
    }
}
