<?php

namespace App\Console;

use App\MatchesRound;
use App\Team;
use App\TeamFundMovement;
use App\Tournament;
use App\TournamentPosition;
use App\TournamentRound;
use App\Player;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
                ->before(function() {
                    /**
                     * Calculate match incomes
                     */
                    $rounds = TournamentRound::whereBetween('datetime', [Carbon::now()->startOfDay()->getTimestamp(), Carbon::now()->endOfDay()->getTimestamp()])->get();
                    foreach ($rounds as $round) {
                        $top_points = TournamentPosition::where('category_id', $round['category_id'])->where('position', 1)->first();
                        $matches = MatchesRound::where('round_id', $round['id'])->get();
                        foreach ($matches as $match) {
                            $local = TournamentPosition::where('category_id', $round['category_id'])->where('team_id', $match->local_id)->first();
                            $visit = TournamentPosition::where('category_id', $round['category_id'])->where('team_id', $match->visit_id)->first();
                            $local_assistance = max(0.1, (100 - ((int)(($top_points->points - $local->points) * 2))) / 100);
                            $visit_assistance = max(0.1, (100 - ((int)(($top_points->points - $visit->points) * 2))) / 100);
                            $total_assistance = (0.6 * $local_assistance) + (0.4 * (($local_assistance + $visit_assistance) / 2));

                            $match->assistance = (int)(\Config::get('constants.STADIUM_SIZE') * $total_assistance);
                            $match->incomes = $match->assistance * \Config::get('constants.TICKET_VALUE');
                            $match->save();

                            $incomes = (int)($match->incomes / 2);
                            $local = Team::find($match->local_id);
                            $local->moneyMovement($incomes, 'Ingresos por venta de entradas');
                            $visit = Team::find($match->visit_id);
                            $visit->moneyMovement($incomes, 'Ingresos por venta de entradas');
                        }
                    }
                })
                ->after(function() {
                    /**
                     * Upgrade players
                     */
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

        /**
         * Close opened tournaments
         */
        $schedule->call(function() {
                    $tournament = Tournament::where('closed', FALSE)->first();
                    if ($tournament && !$tournament->tournamentCategories[0]->isOpen) {
                        $tournament->close();
                    }
                })
                ->cron('0 12 * * 6 *')
                ->appendOutputTo('/var/log/futbolin/tournament.log');

        /**
         * Run weekly team maintenance
         */
        $schedule->call(function() {
                    $teams = Team::where('id', '>=', 27)->get();
                    foreach ($teams as $team) {
                        $team->paySalaries();
                    }
                })
                ->cron('0 20 * * 7 *')
                ->appendOutputTo('/var/log/futbolin/weekly_maintenance.log');

        /**
         * Delete old matches log files
         */
        $schedule->exec('find ' . base_path() . '/python/logs/ -type f -mtime +90 -name \'*.log\' -delete')
                ->monthly()
                ->appendOutputTo('/var/log/futbolin/old-logs.log');
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
