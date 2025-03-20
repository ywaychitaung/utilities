<?php

namespace App\Console;

use App\Jobs\DeleteExpiredUrlsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Dispatch the job to delete expired URLs daily
        // for cleanup of expired URLs
        $schedule->job(new DeleteExpiredUrlsJob)->dailyAt('01:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
