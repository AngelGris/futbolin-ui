<?php

namespace App\Console;

use App\MarketTransaction;
use App\MatchesRound;
use App\PlayerSelling;
use App\Team;
use App\TeamFundMovement;
use App\Tournament;
use App\TournamentCategory;
use App\TournamentPosition;
use App\TournamentRound;
use App\Notification;
use App\Player;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Storage;

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
         * Every minute tasks
         */
        $schedule->call(function() {
            // Transfer players
            $sellings = PlayerSelling::where('closes_at', '<', Carbon::now())->get();
            foreach ($sellings as $selling) {
                // If the player has a good offer complete teh transaction
                // Else if player has team, just finish transferable period
                // Else (free agent) renew transferable period if it has been free for less than 4 weeks
                // Otherwise delete it
                if ($selling->best_offer_value > $selling->value) {
                    $selling_team = $selling->player->team;
                    if ($selling->player->transfer($selling->offeringTeam, $selling->best_offer_value)) {
                        if ($selling_team) {
                            $selling_team->moneyMovement($selling->best_offer_value, 'Venta de ' . $selling->player->first_name . ' ' . $selling->player->last_name);
                        }
                        $selling->offeringTeam->moneyMovement(-$selling->best_offer_value, 'Compra de ' . $selling->player->first_name . ' ' . $selling->player->last_name);

                        MarketTransaction::create([
                            'player_id'     => $selling->player->id,
                            'seller_id'     => $selling_team ? $selling_team->id : NULL,
                            'buyer_id'      => $selling->offeringTeam->id,
                            'value'         => $selling->best_offer_value,
                            'created_at'    => Carbon::now()
                        ]);
                    }
                    $selling->delete();
                } elseif ($selling->player->team) {
                    // Notify selling team
                    Notification::create([
                        'user_id' => $selling->player->team->user->id,
                        'title' => 'No hubo ninguna oferta por ' . $selling->player->first_name . ' ' . $selling->player->last_name,
                        'message' => 'No has podido vender a <a href="/jugador/' . $selling->player->id . '/">' . $selling->player->first_name . ' ' . $selling->player->last_name . '</a> y continua a disposición de tu cuerpo técnico.',
                    ]);
                    $selling->delete();
                } elseif ($selling->created_at > Carbon::now()->subWeeks(4)) {
                    $selling->closes_at = Carbon::now()->addDays(\Config::get('constants.PLAYERS_TRANSFERABLE_PERIOD'));
                    $selling->save();
                } else {
                    $selling->player->delete();
                    $selling->delete();
                }
            }
        })
        ->appendOutputTo('/var/log/futbolin/every_minute.log');

        $schedule->call(function() {
            // Generate free players
            $generation_rate = \Config::get('constants.FREE_PLAYERS_GENERATE') * 41.67; // (41.67 = 1000 / 24)
            $rand = mt_rand(1, 1000);

            if ($rand < $generation_rate) {
                $value = mt_rand(1, 10);
                switch ($value) {
                    case 1:
                        $position = 'ARQ';
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $position = 'DEF';
                        break;
                    case 5:
                    case 6:
                    case 7:
                        $position = 'MED';
                        break;
                    default:
                        $position = 'ATA';
                        break;
                }
                $player = Player::create(NULL, $value, $position);

                PlayerSelling::create([
                    'player_id'         => $player->id,
                    'value'             => $player->value,
                    'best_offer_value'  => $player->value,
                    'closes_at'         => Carbon::now()->addDays(\Config::get('constants.PLAYERS_TRANSFERABLE_PERIOD'))
                ]);
            }
        })
        ->hourly()
        ->appendOutputTo('/var/log/futbolin/free_players.log');

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
                            if ($match->local_id >= 27) {
                                $local = Team::find($match->local_id);
                                $local->moneyMovement($incomes, 'Ingresos por venta de entradas');
                            }
                            if ($match->visit_id >= 27) {
                                $visit = Team::find($match->visit_id);
                                $visit->moneyMovement($incomes, 'Ingresos por venta de entradas');
                            }
                        }
                    }
                })
                ->after(function() {
                    /**
                     * Copy logs to S3
                     */
                    $logs = DB::table('matches')->select('logfile')->where('created_at', '>=', Carbon::now()->subMinutes(10))->get();
                    foreach ($logs as $log) {
                        if (!Storage::disk('s3')->exists(env('APP_ENV') . '/logs/' . $log->logfile)) {
                            Storage::disk('s3')->put(env('APP_ENV') . '/logs/' . $log->logfile, file_get_contents(base_path() . '/python/logs/' . $log->logfile));
                        }
                    }

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
                    // Pesonal trainers
                    $teams = Team::where('trainer', '>=', Carbon::now())->get();
                    foreach ($teams as $team) {
                        $team->train(TRUE);
                    }

                    // Train free players
                    $players = Player::whereNull('team_id')->get();
                    DB::table('players')->whereNull('team_id')->where('recovery', 0)->whereNull('deleted_at')->increment('experience', 10);
                    $players = Player::where('experience', '>=', 100)->get();
                    foreach ($players as $player) {
                        $player->upgrade();
                    }

                    // recover stamina
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
                    // Pay salaries to teams in tournaments
                    $categories = DB::table('tournament_categories')->select('id')->where('tournament_id', function($query) {
                        $query->from('tournament_categories')->selectRaw('MAX(tournament_id)');
                    })->get();
                    $cats = [];
                    foreach ($categories as $category) {
                        $cats[] = $category->id;
                    }
                    $teams = Team::select('teams.*')->join('tournament_positions', 'teams.id', '=', 'tournament_positions.team_id')->where('teams.user_id', '>', 1)->whereIn('tournament_positions.category_id', $cats)->get();
                    foreach ($teams as $team) {
                        $team->paySalaries();
                    }
                })
                ->cron('0 20 * * 7 *')
                ->appendOutputTo('/var/log/futbolin/weekly_maintenance.log');

        /**
         * Delete old matches log files
         */
        $schedule->exec('find ' . base_path() . '/python/logs/ -type f -mtime +30 -name \'*.log\' -delete')
                ->daily()
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
