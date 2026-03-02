<?php

namespace App\Console\Commands;

use App\Models\Winner;
use Illuminate\Console\Command;

class LotteryPublishWinners extends Command
{
    protected $signature = 'lottery:publish-winners';

    protected $description = 'Publish pending winners.';

    public function handle(): int
    {
        $updated = Winner::query()
            ->whereNull('published_at')
            ->update(['published_at' => now()]);

        $this->info("Published {$updated} winner(s).");

        return self::SUCCESS;
    }
}
