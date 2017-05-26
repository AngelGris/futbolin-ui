<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tournament;
use App\Team;

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
        $teams_count = Team::where('user_id', '>', 1)->count();
        $groups = (int)($teams_count / 20);
        if ($teams_count % 20) {
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
        $last_tournament = Tournament::latest()->limit(1)->get();
        $teams_count = Team::where('user_id', '>', 1)->count();
        $teams_added = 0;
        $groups = (int)($teams_count / 20);
        if ($teams_count % 20) {
            $groups++;
        }
        $zones = (int)($groups / $request->categories);
        if ($groups % $request->categories) {
            $zones++;
        }

        $tournament = Tournament::create(['name' => $request->name, 'categories' => $request->categories, 'zones' => $zones]);

        if (empty($last_tournament[0])) {
            for ($i = 0; $i < $request->categories - 1; $i++) {
                $teams = Team::where('user_id', '>', 1)->offset($teams_added)->limit(20 * $zones)->oldest()->get();
                $aux = [];
                foreach ($teams as $team) {
                    $aux[] = $team->id;
                }
                shuffle($aux);
                $teams_added += count($teams);
                for ($j = 0; $j < $zones; $j++) {
                    $tournament->createCategory($i + 1, $j + 1, array_slice($aux, $j * 20, 20));
                }
            }

            if (($teams_count - $teams_added) > 0) {
                $teams = Team::where('user_id', '>', 1)->offset($teams_added)->limit(20 * $zones)->oldest()->get();
                $groups_remaining = (int)(count($teams) / 20);
                if (count($teams) % 20) {
                    $groups_remaining++;
                }
                $sparrings = Team::where('user_id', '=', 1)->limit((20 * $groups_remaining) - count($teams))->inRandomOrder()->get();
                $aux = [];
                foreach ($teams as $team){
                    $aux[] = $team->id;
                }
                foreach ($sparrings as $team) {
                    $aux[] = $team->id;
                }
                shuffle($aux);
                for ($j = 0; $j < $zones; $j++) {
                    $tournament->createCategory($i + 1, $j + 1, array_slice($aux, $j * 20, 20));
                }
            }
        } else {
            echo('not empty');
        }

        return redirect(route('admin.tournaments', getDomain()));
    }
}
