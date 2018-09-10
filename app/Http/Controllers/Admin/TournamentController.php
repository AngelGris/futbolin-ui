<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tournament;
use App\TournamentPosition;
use App\TournamentCategory;
use App\Team;
use Carbon\Carbon;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::orderBy('created_at', 'DESC')->get();

        $vars = [
            'tournaments' => $tournaments,
        ];

        return view('admin.tournament.index', $vars);
    }

    public function show($domain, Tournament $tournament)
    {
        $vars = [
            'tournament' => $tournament,
        ];

        return view('admin.tournament.show', $vars);
    }

    public function create()
    {
        $teams_count = Team::where('user_id', '>', 1)->where('playable', '=', 1)->count();
        $groups = (int)($teams_count / \Config::get('constants.TEAMS_PER_CATEGORY'));
        if ($teams_count % \Config::get('constants.TEAMS_PER_CATEGORY')) {
            $groups++;
        }

        $tournament = [
            'name' => 'Temporada ' . (Tournament::count() + 1),
            'teams' => $teams_count,
            'sparrings' => Team::where('user_id', '=', 1)->count(),
            'groups' => $groups,
        ];

        $vars = [
            'domain' => getDomain(),
            'tournament' => $tournament,
        ];

        return view('admin.tournament.create', $vars);
    }

    public function store(Request $request)
    {
        Tournament::createTournament($request->name, $request->zones);

        return redirect(route('admin.tournaments', getDomain()));
    }
}
