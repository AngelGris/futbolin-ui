<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Tournament;
use App\TournamentCategory;

class TournamentController extends Controller
{
    public function index()
    {
        $vars = [
            'icon' => 'fa fa-trophy',
            'title' => 'Torneos',
            'subtitle' => 'Hora de mostrar quién manda',
        ];

        $category_id =  \DB::table('tournament_categories')
                        ->join('tournament_positions', 'tournament_positions.category_id', '=', 'tournament_categories.id')
                        ->where('tournament_positions.team_id', '=', Auth::user()->team->id)
                        ->max('tournament_categories.id');

        if ($category_id) {
            $category = TournamentCategory::find($category_id);
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
        }

        return view('tournament.index', $vars);
    }
}
