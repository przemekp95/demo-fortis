<?php

use App\Models\Campaign;
use App\Models\CampaignRule;
use App\Models\User;
use Tests\Integration\ProdLike\Support\ConcurrentProcessRunner;
use Tests\Integration\ProdLike\Support\MySqlLock;

it('accepts only one receipt when duplicate submissions race on mysql', function () {
    if (config('database.default') !== 'mysql') {
        $this->markTestSkipped('This prod-like test requires the mysql connection.');
    }

    $campaign = Campaign::create([
        'name' => 'Prod Like Receipt Campaign',
        'slug' => 'prod-like-receipt-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    CampaignRule::create([
        'campaign_id' => $campaign->id,
        'max_entries_per_day' => 5,
        'velocity_per_hour' => 3,
        'max_receipt_age_days' => 14,
        'min_purchase_amount' => 1,
        'deduplicate_receipts' => true,
        'risk_score_flag_threshold' => 60,
        'risk_score_reject_threshold' => 90,
    ]);

    $firstUser = User::factory()->create(['email_verified_at' => now()]);
    $secondUser = User::factory()->create(['email_verified_at' => now()]);
    $receiptPayload = [
        'receipt_number' => 'PROD-LIKE-DUP-001',
        'purchase_amount' => 59.90,
        'purchase_date' => now()->toDateString(),
        'device_fingerprint' => 'prod-like-device',
    ];

    $lock = MySqlLock::lockRow('campaigns', $campaign->id);

    $firstProcess = ConcurrentProcessRunner::start('tests/Integration/ProdLike/bin/submit_receipt.php', [
        'user_id' => $firstUser->id,
        'receipt' => $receiptPayload,
        'ip' => '10.0.0.10',
    ]);
    $secondProcess = ConcurrentProcessRunner::start('tests/Integration/ProdLike/bin/submit_receipt.php', [
        'user_id' => $secondUser->id,
        'receipt' => $receiptPayload,
        'ip' => '10.0.0.11',
    ]);

    usleep(250000);
    $lock->commit();

    $firstResult = ConcurrentProcessRunner::wait($firstProcess);
    $secondResult = ConcurrentProcessRunner::wait($secondProcess);

    $statuses = collect([$firstResult, $secondResult])->pluck('status')->sort()->values()->all();

    expect($statuses)->toBe(['success', 'validation_error']);

    $this->assertDatabaseCount('receipts', 1);
    $this->assertDatabaseCount('entries', 1);
    $this->assertDatabaseHas('receipts', [
        'campaign_id' => $campaign->id,
        'receipt_number' => 'PROD-LIKE-DUP-001',
    ]);
});
