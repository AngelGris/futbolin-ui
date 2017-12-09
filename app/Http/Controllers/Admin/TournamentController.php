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
        $teams_count = Team::where('user_id', '>', 1)->where('playable', '=', 1)->count();
        $teams_added = 0;
        $groups = (int)($teams_count / 20);
        if ($teams_count % 20) {
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
                $teams = Team::where('user_id', '>', 1)->where('playable', '=', 1)->offset($teams_added)->limit(20 * $zones)->oldest()->get();
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
                $teams = Team::where('user_id', '>', 1)->where('playable', '=', 1)->offset($teams_added)->limit(20 * $zones)->oldest()->get();
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
            for ($i = count($teams);  $i > 0; $i--) {
                $staying = $degrading = [];
                foreach($teams[$i] as $team) {
                    if (!is_null($team->user->last_activity) and (Carbon::now()->timestamp - $team->user->last_activity->timestamp) < 2592000) {
                        $staying[] = $team;
                    } else {
                        $degrading[] = $team;
                    }
                }

                while (count($degrading) < 3 and count($staying) > 3) {
                    $degrading[] = array_pop($staying);
                }

                /**
                 * Teams keeping category and degrading
                 */
                $teams[$i] = $staying;
                if (empty($teams[$i + 1])) {
                    $teams[$i + 1] = $degrading;
                } else{
                    $teams[$i + 1] = array_merge($teams[$i + 1], $degrading);
                }

                /**
                 * Promote teams from lower category
                 */
                $missing_teams = (20 * $zones) - count($teams[$i]);
                $teams[$i] = array_merge($teams[$i], array_slice($teams[$i + 1], 0, $missing_teams));
                $teams[$i + 1] = array_slice($teams[$i + 1], $missing_teams);
            }

            for ($i = 1; $i < $categories; $i++) {
                /**
                 * Make sure each category has 20x teams
                 */
                if (count($teams[$i]) < 20 * $zones) {
                    $diff = (20 * $zones) - count($teams[$i]);
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
                    $tournament->createCategory($i, $j + 1, array_slice($aux, $j * 20, 20));
                }
            }

            /**
             * Complete last category with sparrings
             */
            if (($teams_count - $teams_added) > 0) {
                $groups_remaining = (int)(count($teams[$categories]) / 20);
                if (count($teams[$categories]) % 20) {
                    $groups_remaining++;
                }

                $sparrings = Team::where('user_id', '=', 1)->limit((20 * $groups_remaining) - count($teams[$categories]))->inRandomOrder()->get();
                foreach ($sparrings as $team) {
                    $teams[$categories][] = $team;
                }
                shuffle($teams[$categories]);

                for ($j = 0; $j < $zones; $j++) {
                    $tournament->createCategory($i, $j + 1, array_slice($teams[$categories], $j * 20, 20));
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
        $players = \DB::table('players')
                      ->select(['id', 'age'])
                      ->where('team_id', '>', 1)
                      ->where('age', '>=', 33)
                      ->where('retiring', '=', FALSE)
                      ->whereNull('deleted_at')
                      ->get();
        $players_retiring = [];
        foreach ($players as $player) {
            switch ($player->age) {
                case '33':
                    $limit = 10;
                    break;
                case '34':
                    $limit = 25;
                    break;
                case '35':
                    $limit = 60;
                    break;
                case '36':
                    $limit = 75;
                    break;
                case '37':
                    $limit = 85;
                    break;
                case '38':
                    $limit = 95;
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
                $players_retiring[] = $player->id;
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

        return redirect(route('admin.tournaments', getDomain()));
    }
}
