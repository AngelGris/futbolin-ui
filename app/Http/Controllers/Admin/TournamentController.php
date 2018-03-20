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
            $teams = [];

            /**
             * Load teams from the last tournament
             */
            foreach ($last_tournament[0]->tournamentCategories as $category) {
                $positions = TournamentPosition::where('category_id', '=', $category->id)->orderBy('position')->get();

                $count = 0;
                $max_category = tournamentCategory::orderBy('tournament_id', 'DESC')->orderBy('category', 'DESC')->first();
                foreach ($positions as $position) {
                    $team = Team::find($position->team_id);
                    if ($team->user_id > 1) {
                        $count++;
                        $teams[$category->category][] = $team;
                    }
                }
            }

            /**
             * Add newly created teams to the last category
             */
            $teams_list = [];
            foreach ($teams as $cat) {
                $teams_listing = array_map(function ($entry) {
                  return $entry['id'];
                }, $cat);

                $teams_list = array_merge($teams_list, $teams_listing);
            }

            $new_teams = Team::where('user_id', '>', 1)->whereNotIn('id', $teams_list)->where('playable', '=', 1)->get();
            foreach ($new_teams as $team) {
                $teams[$categories][] = $team;
            }

            /**
             * Check inactive users and degraded teams
             */
            $degrading_prev = [];
            for ($i = 1; $i <= $groups - 0; $i++) {
                $staying = $degrading = [];
                foreach($teams[$i] as $team) {
                    if (!is_null($team->user->last_activity) and (Carbon::now()->timestamp - $team->user->last_activity->timestamp) < \Config::get('constants.USER_INACTIVE')) {
                        $staying[] = $team;
                    } else {
                        $degrading[] = $team;
                    }
                }

                while ((count($staying) + count($degrading_prev)) > ((\Config::get('constants.TEAMS_PER_CATEGORY')) - \Config::get('constants.DEGRADES_PER_CATEGORY'))) {
                    if (
                        count($staying) == 0 ||
                        (
                            count($degrading_prev) > 0 &&
                            (
                                is_null($degrading_prev[count($degrading_prev) - 1]->user->last_activity) ||
                                (Carbon::now()->timestamp - $degrading_prev[count($degrading_prev) - 1]->user->last_activity->timestamp) < \Config::get('constants.USER_INACTIVE')
                            )
                        )
                    ) {
                        array_unshift($degrading, array_pop($degrading_prev));
                    } else {
                        array_unshift($degrading, array_pop($staying));
                    }
                }

                /**
                 * Teams keeping category and degrading
                 */
                $teams[$i] = array_merge($staying, $degrading_prev);
                $degrading_prev = $degrading;
            }

            if (!empty($degrading_prev)) {
                $teams[$groups + 1] = $degrading_prev;
            }

            for ($i = 1; $i <= $groups; $i++) {
                /**
                 * Promote teams from lower category
                 */
                $missing_teams = (\Config::get('constants.TEAMS_PER_CATEGORY') * $zones) - count($teams[$i]);
                $teams[$i] = array_merge($teams[$i], array_slice($teams[$i + 1], 0, $missing_teams));
                $teams[$i + 1] = array_slice($teams[$i + 1], $missing_teams);
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
         * Players aging
         */
        $sql = \DB::table('players')
           ->where('team_id', '>', 1)
           ->whereNull('deleted_at')
           ->increment('age');

        /**
         * Retire players and create young players
         */
        $players = \DB::table('players')
                      ->select(['id', 'team_id'])
                      ->where('retiring', '=', TRUE)
                      ->whereNull('deleted_at')
                      ->get();
        $teams = [];
        foreach ($players as $player) {
            Team::find($player->team_id)->replacePlayer($player->id);
        }

        /**
         * Players retiring next tournament
         */
        $teams = Team::where('user_id', '>', 1)->get();
        $players_retiring = [];
        foreach ($teams as $team) {
            $players = $team->players()
                      ->where('age', '>=', 32)
                      ->where('retiring', '=', FALSE)
                      ->whereNull('deleted_at')
                      ->orderBy('age', 'DESC')
                      ->get();

            $team_retiring = FALSE; // This team has a player retiring?
            foreach ($players as $player) {
                switch ($player->age) {
                    case '32':
                        $limit = 10;
                        break;
                    case '33':
                        $limit = 25;
                        break;
                    case '34':
                        $limit = 60;
                        break;
                    case '35':
                        $limit = 75;
                        break;
                    default:
                        $limit = 100;
                        break;
                }

                $retiring = TRUE;
                if ($limit < 100) {
                    $num = mt_rand(1, 100);
                    if ($num > $limit) {
                        $retiring = FALSE;
                    }
                }

                if ($retiring) {
                    $team_retiring = TRUE;
                    $players_retiring[] = $player->id;
                }
            }

            /**
             * Make sure each team with at least 3 players in retiring age
             * has at least 1 player retiring
             */
            if (count($players) >= 3 && !$team_retiring) {
                $players_retiring[] = $players[0]->id;
            }
        }
        \DB::table('players')
           ->whereIn('id', $players_retiring)
           ->update(['retiring' => true]);

        /**
         * Reset players stamina
         */
        \DB::table('players')
           ->update(['stamina' => 100]);

        /**
         * Delete yellow cards
         */
        \DB::table('player_cards')->truncate();

        return redirect(route('admin.tournaments', getDomain()));
    }
}
