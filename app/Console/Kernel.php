<?php

namespace App\Console;

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
        $schedule->exec('python3 ' . base_path() . '/python/cron.py')
                 ->cron('0 20 * * 1,3,5 *')
                 ->sendOutputTo('/var/log/futbolin/cron-' . date('Ymdhis') . '.log')
                 ->after(function() {
                    $players = Player::where('experience', '>=', 100)->get();
                    foreach($players as $player) {
                        $player->upgrade();
                    }
                 });
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
