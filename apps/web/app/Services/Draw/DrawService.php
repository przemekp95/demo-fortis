<?php

namespace App\Services\Draw;

use App\Events\DrawCompleted;
use App\Events\WinnerPublished;
use App\Models\DrawRun;
use App\Models\DrawSchedule;
use App\Models\Entry;
use App\Models\Winner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class DrawService
{
    public function runDueSchedules(): int
    {
        $processed = 0;

        DrawSchedule::query()
            ->where('status', 'pending')
            ->where('run_at', '<=', now())
            ->orderBy('run_at')
            ->pluck('id')
            ->each(function (int $scheduleId) use (&$processed): void {
                $schedule = DrawSchedule::query()->find($scheduleId);

                if ($schedule === null) {
                    return;
                }

                $drawRun = $this->runSchedule($schedule);

                if ($drawRun->wasRecentlyCreated) {
                    $processed++;
                }
            });

        return $processed;
    }

    public function runSchedule(DrawSchedule $schedule): DrawRun
    {
        return DB::transaction(function () use ($schedule): DrawRun {
            $lockedSchedule = DrawSchedule::query()
                ->whereKey($schedule->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingRun = DrawRun::query()
                ->where('draw_schedule_id', $lockedSchedule->id)
                ->latest('id')
                ->first();

            if ($existingRun !== null) {
                return $existingRun;
            }

            if (! in_array($lockedSchedule->status, ['pending', 'running'], true)) {
                throw new RuntimeException(sprintf('Draw schedule %d cannot be processed from status %s.', $lockedSchedule->id, $lockedSchedule->status));
            }

            if ($lockedSchedule->status === 'pending') {
                $lockedSchedule->update(['status' => 'running']);
            }

            $drawRun = DrawRun::create([
                'campaign_id' => $lockedSchedule->campaign_id,
                'draw_schedule_id' => $lockedSchedule->id,
                'type' => $lockedSchedule->type,
                'idempotency_key' => sprintf('draw_schedule_%d_%s', $lockedSchedule->id, Str::uuid()),
                'status' => 'running',
                'started_at' => now(),
            ]);

            $eligibleEntries = Entry::query()
                ->where('campaign_id', $lockedSchedule->campaign_id)
                ->where('status', 'approved')
                ->whereDoesntHave('winner')
                ->lockForUpdate()
                ->inRandomOrder()
                ->get();

            $prizes = $lockedSchedule->campaign->prizes()
                ->where('draw_type', $lockedSchedule->type)
                ->get();

            foreach ($prizes as $prize) {
                for ($i = 0; $i < $prize->quantity; $i++) {
                    $entry = $eligibleEntries->shift();
                    if ($entry === null) {
                        break;
                    }

                    $winner = Winner::create([
                        'campaign_id' => $lockedSchedule->campaign_id,
                        'draw_run_id' => $drawRun->id,
                        'prize_id' => $prize->id,
                        'entry_id' => $entry->id,
                        'user_id' => $entry->user_id,
                        'status' => 'pending_contact',
                        'published_at' => now(),
                    ]);

                    $entry->update(['status' => 'winner']);

                    event(new WinnerPublished($winner));
                }
            }

            $drawRun->update([
                'status' => 'completed',
                'finished_at' => now(),
                'metadata' => ['winner_count' => $drawRun->winners()->count()],
            ]);

            $lockedSchedule->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            event(new DrawCompleted($drawRun));

            return $drawRun->refresh();
        });
    }
}
