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
        $last_tournament = Tournament::latest()->limit(1)->get();
        $teams_count = Team::where('user_id', '>', 1)->where('playable', '=', 1)->count();
        $teams_added = 0;
        $groups = (int)($teams_count / \Config::get('constants.TEAMS_PER_CATEGORY'));
        if ($teams_count % \Config::get('constants.TEAMS_PER_CATEGORY')) {
            $groups++;
        }
        $zones = $request->zones;
        $categories = (int)($groups / $request->zones);
        if ($groups % $request->zones) {
            $categories++;
        }

        $tournament = Tournament::create(['name' => $request->name, 'categories' => $categories, 'zones' => $zones]);

        if (empty($last_tournament[0])) {
            for ($i = 0; $i < $categories - 1; $i++) {
                $teams = Team::where('user_id', '>', 1)->where('playable', '=', 1)->offset($teams_added)->limit(\Config::get('constants.TEAMS_PER_CATEGORY') * $zones)->oldest()->get();
                $aux = [];
                foreach ($teams as $team) {
                    $aux[] = $team->id;
                }
                shuffle($aux);
                $teams_added += count($teams);
                for ($j = 0; $j < $zones; $j++) {
                    $tournament->createCategory($i + 1, $j + 1, array_slice($aux, $j * \Config::get('constants.TEAMS_PER_CATEGORY'), \Config::get('constants.TEAMS_PER_CATEGORY')));
                }
            }

            if (($teams_count - $teams_added) > 0) {
                $teams = Team::where('user_id', '>', 1)->where('playable', '=', 1)->offset($teams_added)->limit(\Config::get('constants.TEAMS_PER_CATEGORY') * $zones)->oldest()->get();
                $groups_remaining = (int)(count($teams) / \Config::get('constants.TEAMS_PER_CATEGORY'));
                if (count($teams) % \Config::get('constants.TEAMS_PER_CATEGORY')) {
                    $groups_remaining++;
                }
                $sparrings = Team::where('user_id', '=', 1)->limit((\Config::get('constants.TEAMS_PER_CATEGORY') * $groups_remaining) - count($teams))->inRandomOrder()->get();
                $aux = [];
                foreach ($teams as $team){
                    $aux[] = $team->id;
                }
                foreach ($sparrings as $team) {
                    $aux[] = $team->id;
                }
                shuffle($aux);
                for ($j = 0; $j < $zones; $j++) {
                    $tournament->createCategory($i + 1, $j + 1, array_slice($aux, $j * \Config::get('constants.TEAMS_PER_CATEGORY'), \Config::get('constants.TEAMS_PER_CATEGORY')));
                }
            }
        } else {
            $teams = []; // Teams divided in categories
            $teams_listing = []; // Teams IDs

            /**
             * Load teams from the last tournament
             * divided in active and inactive teams
             */
            foreach ($last_tournament[0]->tournamentCategories as $category) {
                $positions = TournamentPosition::where('category_id', '=', $category->id)->orderBy('position')->get();

                $max_category = tournamentCategory::orderBy('tournament_id', 'DESC')->orderBy('category', 'DESC')->first();
                $teams[$category->category] = [
                    'upgrading'    => [],
                    'downgrading'  => [],
                ];
                foreach ($positions as $position) {
                    $team = Team::find($position->team_id);
                    if ($team->user_id > 1) {
                        $teams_listing[] = $team->id;
                        if (!is_null($team->user->last_activity) and (Carbon::now()->timestamp - $team->user->last_activity->timestamp) < \Config::get('constants.USER_INACTIVE')) {
                            $teams[$category->category]['upgrading'][] = $team;
                        } else {
                            $teams[$category->category]['downgrading'][] = $team;
                        }
                    }
                }

                if (count($teams[$category->category]['downgrading']) < \Config::get('constants.DEGRADES_PER_CATEGORY')) {
                    $diff = \Config::get('constants.DEGRADES_PER_CATEGORY') - count($teams[$category->category]['downgrading']);
                    $teams[$category->category]['downgrading'] = array_merge(array_slice($teams[$category->category]['upgrading'], -$diff), $teams[$category->category]['downgrading']);
                    $teams[$category->category]['upgrading'] = array_slice(($teams[$category->category]['upgrading']), 0, -$diff);
                }
            }

            /**
             * Add newly created teams to the last category
             */
            $new_teams = Team::where('user_id', '>', 1)->whereNotIn('id', $teams_listing)->where('playable', '=', 1)->get();
            foreach ($new_teams as $team) {
                if (!is_null($team->user->last_activity) and (Carbon::now()->timestamp - $team->user->last_activity->timestamp) < \Config::get('constants.USER_INACTIVE')) {
                    $teams[$categories]['upgrading'][] = $team;
                } else {
                    $teams[$categories]['downgrading'][] = $team;
                }
            }

            /**
             * Upgrade teams from bottom to top (Bubbling)
             * always prioritize active players
             */
            for ($i = $categories; $i > 1; $i--) {
                $moving = min(count($teams[$i]['upgrading']), count($teams[$i - 1]['downgrading']));
                $teams[$i - 1]['upgrading'] = array_merge($teams[$i - 1]['upgrading'], array_slice($teams[$i]['upgrading'], 0, $moving));
                $teams[$i]['upgrading'] = array_slice(($teams[$i]['upgrading']), $moving);

                $teams[$i]['downgrading'] = array_merge(array_slice($teams[$i - 1]['downgrading'], -$moving), $teams[$i]['downgrading']);
                $teams[$i - 1]['downgrading'] = array_slice(($teams[$i - 1]['downgrading']), 0, -$moving);
            }

            /**
             * Merge upgrades and downgrades keeping the order
             * first upgrades and then downgrades
             */
            for ($i = 1; $i <= $categories; $i++) {
                $teams[$i] = array_merge($teams[$i]['upgrading'], $teams[$i]['downgrading']);
            }

            for ($i = 1; $i < $categories; $i++) {
                /**
                 * Make sure each category has TEAMS_PER_CATEGORY teams
                 */
                if (count($teams[$i]) < \Config::get('constants.TEAMS_PER_CATEGORY') * $zones) {
                    $diff = (\Config::get('constants.TEAMS_PER_CATEGORY') * $zones) - count($teams[$i]);
                    for ($j = 0; $j < $diff; $j++) {
                        $teams[$i][] = $teams[$i + 1][$j];
                        unset($teams[$i + 1][$j]);
                    }
                }

                /**
                 * Shuffle teams and create the new category
                 */
                $aux = [];
                foreach ($teams[$i] as $team) {
                    $aux[] = $team;
                }
                shuffle($aux);
                $teams_added += count($teams[$i]);
                for ($j = 0; $j < $zones; $j++) {
                    $tournament->createCategory($i, $j + 1, array_slice($aux, $j * \Config::get('constants.TEAMS_PER_CATEGORY'), \Config::get('constants.TEAMS_PER_CATEGORY')));
                }
            }

            /**
             * Complete last category with sparrings
             */
            if (($teams_count - $teams_added) > 0) {
                $groups_remaining = (int)(count($teams[$categories]) / \Config::get('constants.TEAMS_PER_CATEGORY'));
                if (count($teams[$categories]) % \Config::get('constants.TEAMS_PER_CATEGORY')) {
                    $groups_remaining++;
                }

                $sparrings = Team::where('user_id', '=', 1)->limit((\Config::get('constants.TEAMS_PER_CATEGORY') * $groups_remaining) - count($teams[$categories]))->inRandomOrder()->get();
                foreach ($sparrings as $team) {
                    $teams[$categories][] = $team;
                }
                shuffle($teams[$categories]);

                for ($j = 0; $j < $zones; $j++) {
                    $tournament->createCategory($i, $j + 1, array_slice($teams[$categories], $j * \Config::get('constants.TEAMS_PER_CATEGORY'), \Config::get('constants.TEAMS_PER_CATEGORY')));
                }
            }
        }

        /**
         * Reset players stamina
         */
        \DB::table('players')
           ->update(['stamina' => 100]);

        return redirect(route('admin.tournaments', getDomain()));
    }
}
