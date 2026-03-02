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

class DrawService
{
    public function runDueSchedules(): int
    {
        $processed = 0;

        DrawSchedule::query()
            ->where('status', 'pending')
            ->where('run_at', '<=', now())
            ->orderBy('run_at')
            ->get()
            ->each(function (DrawSchedule $schedule) use (&$processed): void {
                $this->runSchedule($schedule);
                $processed++;
            });

        return $processed;
    }

    public function runSchedule(DrawSchedule $schedule): DrawRun
    {
        if ($schedule->status === 'completed') {
            return DrawRun::query()->where('draw_schedule_id', $schedule->id)->latest()->firstOrFail();
        }

        return DB::transaction(function () use ($schedule): DrawRun {
            $schedule->update(['status' => 'running']);

            $drawRun = DrawRun::create([
                'campaign_id' => $schedule->campaign_id,
                'draw_schedule_id' => $schedule->id,
                'type' => $schedule->type,
                'idempotency_key' => sprintf('draw_schedule_%d_%s', $schedule->id, Str::uuid()),
                'status' => 'running',
                'started_at' => now(),
            ]);

            $eligibleEntries = Entry::query()
                ->where('campaign_id', $schedule->campaign_id)
                ->where('status', 'approved')
                ->whereDoesntHave('winner')
                ->inRandomOrder()
                ->get();

            $prizes = $schedule->campaign->prizes()
                ->where('draw_type', $schedule->type)
                ->get();

            foreach ($prizes as $prize) {
                for ($i = 0; $i < $prize->quantity; $i++) {
                    $entry = $eligibleEntries->shift();
                    if ($entry === null) {
                        break;
                    }

                    $winner = Winner::create([
                        'campaign_id' => $schedule->campaign_id,
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

            $schedule->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            event(new DrawCompleted($drawRun));

            return $drawRun;
        });
    }
}
