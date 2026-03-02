<?php

use App\Models\Campaign;
use App\Models\CampaignRule;
use App\Models\DrawSchedule;
use App\Models\Entry;
use App\Models\Prize;
use App\Models\Receipt;
use App\Models\User;
use App\Services\Draw\DrawService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates winners for due schedule', function () {
    $campaign = Campaign::create([
        'name' => 'Test',
        'slug' => 'test-draw',
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
        'name' => 'Daily Prize',
        'draw_type' => 'daily',
        'quantity' => 1,
        'value' => 100,
    ]);

    $user = User::factory()->create();
    $receipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'A-1',
        'purchase_amount' => 20,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    $entry = Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $receipt->id,
        'user_id' => $user->id,
        'status' => 'approved',
        'risk_score' => 10,
    ]);

    $schedule = DrawSchedule::create([
        'campaign_id' => $campaign->id,
        'type' => 'daily',
        'run_at' => now()->subMinute(),
        'status' => 'pending',
    ]);

    $drawRun = app(DrawService::class)->runSchedule($schedule);

    expect($drawRun->status)->toBe('completed');
    $entry->refresh();
    expect($entry->status)->toBe('winner');
});
