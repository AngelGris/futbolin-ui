<?php

namespace App\Console;

use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
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
                 ->sendOutputTo('/var/log/futbolin/cron-' . date('YmdHis') . '.log')
                 ->after(function() {
                    $players = Player::where('experience', '>=', 100)->get();
                    foreach($players as $player) {
                        $player->upgrade();
                    }

                    DB::table('players')->where('recovery', '=', 1)->update(['recovery' => 0, 'injury_id' => null]);
                    DB::table('players')->where('recovery', '>', 1)->decrement('recovery');
                 });

        /**
         *  Increase player's stamina by 10 points
         */
        $schedule->call(function() {
                    DB::table('players')->where('stamina', '>=', 90)->update(['stamina' => 100]);
                    DB::table('players')->where('stamina', '<', 90)->increment('stamina', 10);
                 })
                 ->daily()
                 ->sendOutputTo('/var/log/futbolin/stamina-' . date('YmdHis') . '.log');
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
