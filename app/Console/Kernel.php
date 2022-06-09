<?php

namespace App\Console;

use App\Console\Commands\UpdatePrices;
use App\Console\Commands\DeleteOldPrices;
use App\Console\Commands\SendPriceAlerts;
use App\Console\Commands\DeleteOldProducts;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\DownloadProductImages;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(new SendPriceAlerts)->dailyAt('08:30');
        $schedule->call(new DownloadProductImages)->dailyAt('06:00');
        $schedule->call(new DeleteOldProducts)->monthly();
        $schedule->call(new DeleteOldPrices)->monthly();
        $schedule->call(new UpdatePrices)->daily();
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
