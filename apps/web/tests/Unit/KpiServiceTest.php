<?php

use App\Models\Campaign;
use App\Models\DrawRun;
use App\Models\Entry;
use App\Models\Prize;
use App\Models\Receipt;
use App\Models\User;
use App\Models\Winner;
use App\Services\Kpi\KpiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('creates or updates a KPI snapshot for the active campaign and current hour', function () {
    Carbon::setTestNow('2026-03-14 10:15:00');

    $campaign = Campaign::create([
        'name' => 'KPI Campaign',
        'slug' => 'kpi-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $prize = Prize::create([
        'campaign_id' => $campaign->id,
        'name' => 'KPI Prize',
        'draw_type' => 'daily',
        'quantity' => 1,
        'value' => 99,
    ]);

    $user = User::factory()->create();

    $approvedReceipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'KPI-APPROVED',
        'purchase_amount' => 20,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $approvedReceipt->id,
        'user_id' => $user->id,
        'status' => 'approved',
        'risk_score' => 5,
    ]);

    $flaggedReceipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'KPI-FLAGGED',
        'purchase_amount' => 21,
        'purchase_date' => now()->toDateString(),
        'status' => 'flagged',
    ]);

    Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $flaggedReceipt->id,
        'user_id' => $user->id,
        'status' => 'flagged',
        'risk_score' => 75,
        'flagged_at' => now(),
    ]);

    $winnerReceipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'KPI-WINNER',
        'purchase_amount' => 22,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    $winnerEntry = Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $winnerReceipt->id,
        'user_id' => $user->id,
        'status' => 'winner',
        'risk_score' => 15,
    ]);

    $drawRun = DrawRun::create([
        'campaign_id' => $campaign->id,
        'type' => 'daily',
        'idempotency_key' => 'kpi-draw-run',
        'status' => 'completed',
        'started_at' => now()->subHour(),
        'finished_at' => now()->subMinutes(30),
    ]);

    Winner::create([
        'campaign_id' => $campaign->id,
        'draw_run_id' => $drawRun->id,
        'prize_id' => $prize->id,
        'entry_id' => $winnerEntry->id,
        'user_id' => $user->id,
        'status' => 'notified',
        'published_at' => now(),
    ]);

    $snapshot = app(KpiService::class)->snapshot();

    expect($snapshot->metrics)->toMatchArray([
        'entries_total' => 3,
        'entries_flagged' => 1,
        'entries_rejected' => 0,
        'winners_total' => 1,
    ]);

    Entry::query()->where('status', 'approved')->update(['status' => 'rejected', 'rejected_at' => now()]);

    $updatedSnapshot = app(KpiService::class)->snapshot();

    expect($updatedSnapshot->id)->toBe($snapshot->id)
        ->and($updatedSnapshot->metrics['entries_rejected'])->toBe(1);

    $this->assertDatabaseCount('kpi_snapshots', 1);

    Carbon::setTestNow();
});
