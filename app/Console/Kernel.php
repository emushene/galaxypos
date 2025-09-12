<?php

namespace App\Console;

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
        Commands\AutoPurchase::class,
        Commands\DsoAlert::class,
        Commands\ResetDB::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('purchase:auto')->everyFiveMinutes();
        $schedule->command('dsoalert:find')->dailyAt('00:00');
        $schedule->command('reset:db')->everyMinute();
        // Testing Purpose
        $schedule->command('quote:daily')->everyMinute();
        // Run once daily at midnight Check Sunscriptions and Trials
        $schedule->command('subscriptions:check')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
