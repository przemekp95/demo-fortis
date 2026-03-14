<?php

use App\Models\Campaign;
use App\Models\CampaignRule;
use App\Models\DrawSchedule;
use App\Models\Entry;
use App\Models\Prize;
use App\Models\Receipt;
use App\Models\User;
use Tests\Integration\ProdLike\Support\ConcurrentProcessRunner;
use Tests\Integration\ProdLike\Support\MySqlLock;

it('returns the same draw run when two workers race the same schedule on mysql', function () {
    if (config('database.default') !== 'mysql') {
        $this->markTestSkipped('This prod-like test requires the mysql connection.');
    }

    $campaign = Campaign::create([
        'name' => 'Prod Like Draw Campaign',
        'slug' => 'prod-like-draw-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    CampaignRule::create([
        'campaign_id' => $campaign->id,
        'max_entries_per_day' => 10,
        'velocity_per_hour' => 10,
        'max_receipt_age_days' => 14,
        'min_purchase_amount' => 1,
        'deduplicate_receipts' => true,
        'risk_score_flag_threshold' => 60,
        'risk_score_reject_threshold' => 90,
    ]);

    Prize::create([
        'campaign_id' => $campaign->id,
        'name' => 'Prod Like Prize',
        'draw_type' => 'daily',
        'quantity' => 1,
        'value' => 100,
    ]);

    $user = User::factory()->create(['email_verified_at' => now()]);
    $receipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'PROD-DRAW-001',
        'purchase_amount' => 25,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $receipt->id,
        'user_id' => $user->id,
        'status' => 'approved',
        'risk_score' => 12,
        'approved_at' => now(),
    ]);

    $schedule = DrawSchedule::create([
        'campaign_id' => $campaign->id,
        'type' => 'daily',
        'run_at' => now()->subMinute(),
        'status' => 'pending',
    ]);

    $lock = MySqlLock::lockRow('draw_schedules', $schedule->id);

    $firstProcess = ConcurrentProcessRunner::start('tests/Integration/ProdLike/bin/run_draw_schedule.php', [
        'schedule_id' => $schedule->id,
    ]);
    $secondProcess = ConcurrentProcessRunner::start('tests/Integration/ProdLike/bin/run_draw_schedule.php', [
        'schedule_id' => $schedule->id,
    ]);

    usleep(250000);
    $lock->commit();

    $firstResult = ConcurrentProcessRunner::wait($firstProcess);
    $secondResult = ConcurrentProcessRunner::wait($secondProcess);

    expect($firstResult['status'])->toBe('success')
        ->and($secondResult['status'])->toBe('success')
        ->and($secondResult['draw_run_id'])->toBe($firstResult['draw_run_id']);

    $this->assertDatabaseCount('draw_runs', 1);
    $this->assertDatabaseCount('winners', 1);
    $this->assertDatabaseHas('draw_schedules', [
        'id' => $schedule->id,
        'status' => 'completed',
    ]);
});
