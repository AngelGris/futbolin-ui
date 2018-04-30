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
                    'active'    => [],
                    'inactive'  => [],
                    'degraded'  => 0
                ];
                foreach ($positions as $position) {
                    $team = Team::find($position->team_id);
                    if ($team->user_id > 1) {
                        $teams_listing[] = $team->id;
                        if (!is_null($team->user->last_activity) and (Carbon::now()->timestamp - $team->user->last_activity->timestamp) < \Config::get('constants.USER_INACTIVE')) {
                            $teams[$category->category]['active'][] = $team;
                        } else {
                            $teams[$category->category]['inactive'][] = $team;
                        }
                    }
                }
            }

            /**
             * Add newly created teams to the last category
             */
            $new_teams = Team::where('user_id', '>', 1)->whereNotIn('id', $teams_listing)->where('playable', '=', 1)->get();
            foreach ($new_teams as $team) {
                if (!is_null($team->user->last_activity) and (Carbon::now()->timestamp - $team->user->last_activity->timestamp) < \Config::get('constants.USER_INACTIVE')) {
                    $teams[$categories]['active'][] = $team;
                } else {
                    $teams[$categories]['inactive'][] = $team;
                }
            }

            /**
             * Upgrade teams from bottom to top (Bubbling)
             * always prioritize active players
             */
            for ($i = $categories; $i > 1; $i--) {
                if (count($teams[$i]['active']) > 0 and count($teams[$i - 1]['inactive'])) {
                    $moving = min(count($teams[$i]['active']), count($teams[$i - 1]['inactive']));
                    $teams[$i - 1]['active'] = array_merge($teams[$i - 1]['active'], array_slice($teams[$i]['active'], 0, $moving));
                    $teams[$i]['active'] = array_slice(($teams[$i]['active']), $moving);

                    $teams[$i]['inactive'] = array_merge($teams[$i]['inactive'], array_slice($teams[$i - 1]['inactive'], 0, $moving));
                    $teams[$i - 1]['inactive'] = array_slice(($teams[$i - 1]['inactive']), $moving);

                    $teams[$i - 1]['degraded'] = $moving;
                }
            }

            /**
             * Complete downgrade from top to bottom
             */
            for ($i = 1; $i < $categories; $i++) {
                if (
                    $teams[$i]['degraded'] < \Config::get('constants.DEGRADES_PER_CATEGORY') and
                    count($teams[$i + 1]['active']) > 0
                ) {
                    $moving = min(\Config::get('constants.DEGRADES_PER_CATEGORY') - $teams[$i]['degraded'], count($teams[$i + 1]['active']));
                    $teams[$i]['active'] = array_merge(array_slice($teams[$i + 1]['active'], 0, $moving), $teams[$i]['active']);
                    $teams[$i + 1]['active'] = array_slice(($teams[$i + 1]['active']), $moving);

                    while($moving > 0) {
                        if (!empty($teams[$i]['inactive'])) {
                            array_unshift($teams[$i + 1]['inactive'], array_pop($teams[$i]['inactive']));
                        } else {
                            array_unshift($teams[$i + 1]['active'], array_pop($teams[$i]['active']));
                        }
                        $moving--;
                        $teams[$i]['degraded']++;
                    }
                }
            }

            /**
             * Merge active and inactive keeping the order
             * first active and then inactive
             */
            for ($i = 1; $i <= $categories; $i++) {
                $teams[$i] = array_merge($teams[$i]['active'], $teams[$i]['inactive']);
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
