<?php

namespace App;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'closed', 'created_at', 'updated_at'
    ];

    /**
     * Tournament's categories
     */
    public function tournamentCategories()
    {
        return $this->hasMany(TournamentCategory::class);
    }

    /**
     * Close a tournament doing maintenance
     *
     * @return void
     */
    public function close()
    {
        /**
         * Prizes for winners
         */
        foreach($this->tournamentCategories as $category) {
            if ($category->category == 1) {
                $prizes = [5, 3, 2];
            } else {
                $prizes = [3, 2, 1];
            }
            for ($i = 0; $i <= 2; $i++) {
                if ($category->positions[$i]->team->user->id > 1) {
                    if ($category->positions[$i]->team->user->credits <= 255 - $prizes[$i]) {
                        $category->positions[$i]->team->user->credits += $prizes[$i];
                    } else {
                        $category->positions[$i]->team->user->credits = 255;
                    }
                    $category->positions[$i]->team->user->save();
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
        foreach ($players as $player) {
            if ($player->team_id) {
                Team::find($player->team_id)->replacePlayer($player->id);
            } else {
                $player->delete();
            }
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
                      ->orderBy('age', 'DESC')
                      ->get();

            $team_retiring = 0; // This team has a player retiring?
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
                    $team_retiring++;
                    $players_retiring[] = $player->id;

                    if ($team_retiring == 2) {
                        break;
                    }
                }
            }

            /**
             * Make sure each team with at least 3 players in retiring age
             * and at least one with 33 years
             * has at least 1 player retiring
             */
            if (
                !$team_retiring &&
                count($players) >= 3 &&
                $players[0]->age >= 33
            ) {
                $players_retiring[] = $players[0]->id;
            }
        }
        \DB::table('players')
           ->whereIn('id', $players_retiring)
           ->update(['retiring' => true]);

        /**
         * Delete yellow cards
         */
        \DB::table('player_cards')->truncate();

        /**
         * Mark tournaments as closed
         */
        \DB::table('tournaments')->update(['closed' => TRUE]);
    }

    /**
     * @param bool $name
     * @param int $zones
     */
    public static function createTournament($name = FALSE, $zones = 1)
    {
        $season_number = Tournament::count() + 1;
        $last_tournament = Tournament::latest()->limit(1)->get();
        $teams_count = Team::where('user_id', '>', 1)->where('playable', '=', 1)->count();
        $teams_added = 0;
        $groups = (int)($teams_count / \Config::get('constants.TEAMS_PER_CATEGORY'));
        if ($teams_count % \Config::get('constants.TEAMS_PER_CATEGORY')) {
            $groups++;
        }
        $categories = (int)($groups / $zones);
        if ($groups % $zones) {
            $categories++;
        }

        if ($name === FALSE) {
            $name = 'Temporada ' . $season_number;
        }
        $tournament = Tournament::create(['name' => $name, 'categories' => $categories, 'zones' => $zones]);

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
             * Create empty arrays for teams
             */
            for ($i = 1; $i <= $categories; $i++) {
                $teams[$i] = [
                    'upgrading'    => [],
                    'downgrading'  => [],
                ];
            }

            /**
             * Load teams from the last tournament
             * divided in active and inactive teams
             */
            foreach ($last_tournament[0]->tournamentCategories as $category) {
                $positions = TournamentPosition::where('category_id', '=', $category->id)->orderBy('position')->get();

                foreach ($positions as $position) {
                    $team = Team::find($position->team_id);
                    if ($team->user_id > 1) {
                        $teams_listing[] = $team->id;
                        if ($team->user->is_active) {
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
                if ($team->user->is_active) {
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
    }

    /**
     * Create a new category for the tournament
     * @param integer $category
     * @param integer $zone
     * @param integer $teams
     *
     * @return void
     */
    public function createCategory($category, $zone, $teams)
    {
        $season_number = Tournament::count() + 1;
        /**
         * Create category
         */
        $category = $this->tournamentCategories()->create(['category' => $category, 'zone' => $zone]);

        /**
         * Convert teams OBJ to INT
         */
        foreach ($teams as $key => $team) {
            if (is_object($team)) {
                $teams[$key] = [
                    'team_id'   => $team->id,
                    'team_name' => $team->name,
                    'user_id'   => $team->user_id
                ];
            }
        }

        /**
         * Add teams
         */
        $i = 0;
        foreach($teams as $team) {
            TournamentPosition::create(['category_id' => $category->id, 'team_id' => $team['team_id'], 'position' => ++$i]);
        }

        /**
         * Create fixture
         */
        // Tournament starting round
        $round_time = strtotime('next monday') + 72000;
        // Back phase starting round
        $rounds_in_phase = \Config::get('constants.TEAMS_PER_CATEGORY') - 1;
        $phase_days = ((int)(($rounds_in_phase) / 3) * 7) + (($rounds_in_phase % 3) * 2);
        $round_time_back = strtotime(sprintf('next monday +%d days', $phase_days)) + 72000;
        for ($i = 0; $i < $rounds_in_phase; $i++) {
            $round_number = $i + 1;
            $round1 = \DB::table('tournament_rounds')->insertGetId([
                'category_id' => $category->id,
                'number' => $round_number,
                'datetime' =>  $round_time,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $round2 = \DB::table('tournament_rounds')->insertGetId([
                'category_id' => $category->id,
                'number' => $round_number + $rounds_in_phase,
                'datetime' =>  $round_time_back,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $aux = [$teams[0]];
            for ($j = 1; $j < \Config::get('constants.TEAMS_PER_CATEGORY'); $j++) {
                if (($j + $i) < \Config::get('constants.TEAMS_PER_CATEGORY')) {
                    $aux[] = $teams[$j + $i];
                } else {
                    $aux[] = $teams[($j + $i + 1) % \Config::get('constants.TEAMS_PER_CATEGORY')];
                }
            }

            for ($j = 0; $j < \Config::get('constants.TEAMS_PER_CATEGORY') / 2; $j++) {
                if ($i % 2) {
                    $team1 = $aux[$j];
                    $team2 = $aux[\Config::get('constants.TEAMS_PER_CATEGORY') - $j - 1];
                } else {
                    $team1 = $aux[\Config::get('constants.TEAMS_PER_CATEGORY') - $j - 1];
                    $team2 = $aux[$j];
                }

                \DB::table('matches_rounds')->insert([
                    [
                        'round_id' => $round1,
                        'local_id' => $team1['team_id'],
                        'visit_id' => $team2['team_id'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'round_id' => $round2,
                        'local_id' => $team2['team_id'],
                        'visit_id' => $team1['team_id'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                ]);

                if ($round_number == 1) {
                    /**
                     * Notify users about the tournament creation
                     */
                    PushNotification::send(
                        $team1['user_id'],
                        __('notifications.title_7', ['season_number' => $season_number]),
                        __('notifications.message_7', ['rival_name' => $team2['team_name']]),
                        [
                            'screen' => \Config::get('constants.PUSH_NOTIFICATIONS_SCREEN_TOURNAMENT'),
                            'tournament_id' => $category->id
                        ]
                    );

                    PushNotification::send(
                        $team2['user_id'],
                        __('notifications.title_7', ['season_number' => $season_number]),
                        __('notifications.message_7', ['rival_name', $team1['team_name']]),
                        [
                            'screen' => \Config::get('constants.PUSH_NOTIFICATIONS_SCREEN_TOURNAMENT'),
                            'tournament_id' => $category->id
                        ]
                    );
                }
            }

            if ($round_number % 3) {
                $round_time += 172800;
            } else {
                $round_time += 259200;
            }

            if (($rounds_in_phase + $round_number) % 3) {
                $round_time_back += 172800;
            } else {
                $round_time_back += 259200;
            }
        }
    }
}