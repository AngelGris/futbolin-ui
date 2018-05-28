<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return response()->json([
                'tournament'    => $tournament,
                'category'      => $category
            ], 200);
        } else {
            return view('tournament.index', $vars);
        }
    }
}
