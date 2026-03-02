<?php

namespace App\Console\Commands;

use App\Services\Draw\DrawService;
use Illuminate\Console\Command;

class LotteryRunDraws extends Command
{
    protected $signature = 'lottery:run-draws';

    protected $description = 'Run all due draw schedules.';

    public function handle(DrawService $drawService): int
    {
        $processed = $drawService->runDueSchedules();
        $this->info("Processed {$processed} draw schedule(s).");

        return self::SUCCESS;
    }
}
