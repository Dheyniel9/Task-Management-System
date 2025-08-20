<?php

namespace App\Console;

use App\Jobs\CleanupOldTasks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // Here I set up when different background jobs should run
    protected function schedule(Schedule $schedule): void
    {
        // I run the cleanup job every day at midnight (UTC)
        $schedule->job(new CleanupOldTasks())
            ->daily()
            ->at('00:00')
            ->timezone('UTC')
            ->withoutOverlapping()
            ->onOneServer()
            ->runInBackground();
    }

    // Here I register all the custom commands for the app
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
