<?php

namespace App\Console;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Team;
use App\Tournament;
use App\Player;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         *  Run cron to play matches
         *  Monday, Wednesday and Friday at 8pm
         */
        $schedule->exec('python3 ' . base_path() . '/python/cron.py')
                ->cron('0 20 * * 1,3,5 *')
                ->appendOutputTo('/var/log/futbolin/matches.log')
                ->after(function() {
                    $players = Player::where('experience', '>=', 100)->get();
                    foreach($players as $player) {
                        $player->upgrade();
                    }

                    /**
                     * Recover injured players
                     */
                    DB::table('players')->where('recovery', '=', 1)->update(['recovery' => 0, 'injury_id' => null, 'healed' => FALSE]);
                    DB::table('players')->where('recovery', '>', 1)->decrement('recovery');

                    /**
                     * Reduce players' suspensions
                     */
                    DB::table('player_cards')->where('suspension', '=', 1)->update(['suspension' => 0, 'suspension_id' => null]);
                    DB::table('player_cards')->where('suspension', '>', 1)->decrement('suspension');

                    /**
                     * Suspend players with YELLOW_CARDS_SUSPENSION yellow cards
                     */
                    DB::table('player_cards')->where('suspension', '=', 0)->where('cards', '>=', \Config::get('constants.YELLOW_CARDS_SUSPENSION'))->update(['cards' => 0, 'suspension_id' => 1, 'suspension' => 1]);
                });

        /**
         *  Increase player's stamina by 10 points
         */
        $schedule->call(function() {
                    $teams = Team::where('trainer', '>=', Carbon::now())->get();
                    foreach ($teams as $team) {
                        $team->train(TRUE);
                    }

                    DB::table('players')->where('stamina', '>=', 90)->update(['stamina' => 100]);
                    DB::table('players')->where('stamina', '<', 90)->increment('stamina', 10);
                })
                ->daily()
                ->appendOutputTo('/var/log/futbolin/stamina.log');

        $schedule->call(function() {
                    $tournament = Tournament::where('closed', FALSE)->first();
                    if ($tournament && !$tournament->tournamentCategories[0]->isOpen) {
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
                })
                ->cron('0 12 * * 6 *')
                ->appendOutputTo('/var/log/futbolin/tournament.log');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
