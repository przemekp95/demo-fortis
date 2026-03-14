<?php

namespace App\Console;

use App\Jobs\RecomputeKpisJob;
use App\Jobs\RunScheduledDrawsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->job(new RunScheduledDrawsJob)
            ->name('run-scheduled-draws')
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->job(new RecomputeKpisJob)->everyFiveMinutes();
        $schedule->command('lottery:run-retention')->hourly();
        $schedule->command('lottery:publish-winners')->dailyAt('09:00');
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
