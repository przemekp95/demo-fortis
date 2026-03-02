<?php

namespace App\Console\Commands;

use App\Models\DataRetentionJob;
use App\Models\DsrRequest;
use App\Services\Gdpr\DsrService;
use Illuminate\Console\Command;

class LotteryRunRetention extends Command
{
    protected $signature = 'lottery:run-retention';

    protected $description = 'Process pending GDPR retention and DSR jobs.';

    public function handle(DsrService $dsrService): int
    {
        DataRetentionJob::query()
            ->where('status', 'pending')
            ->where('target_date', '<=', now())
            ->each(function (DataRetentionJob $job): void {
                $job->update([
                    'status' => 'completed',
                    'ran_at' => now(),
                ]);
            });

        DsrRequest::query()
            ->where('status', 'requested')
            ->orderBy('requested_at')
            ->get()
            ->each(fn (DsrRequest $request) => $dsrService->process($request));

        $this->info('GDPR jobs processed.');

        return self::SUCCESS;
    }
}
