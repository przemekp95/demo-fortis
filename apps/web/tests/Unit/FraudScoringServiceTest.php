<?php

use App\Models\Campaign;
use App\Models\CampaignRule;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\User;
use App\Services\Fraud\FraudScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

function createCampaignWithRule(array $ruleOverrides = []): array
{
    $campaign = Campaign::create([
        'name' => 'Test Campaign',
        'slug' => 'test-campaign-'.uniqid(),
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $rule = CampaignRule::create(array_merge([
        'campaign_id' => $campaign->id,
        'max_entries_per_day' => 5,
        'velocity_per_hour' => 3,
        'max_receipt_age_days' => 14,
        'min_purchase_amount' => 1,
        'deduplicate_receipts' => true,
        'risk_score_flag_threshold' => 60,
        'risk_score_reject_threshold' => 90,
    ], $ruleOverrides));

    return [$campaign, $rule];
}

function createReceipt(Campaign $campaign, User $user, array $overrides = []): Receipt
{
    return Receipt::create(array_merge([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'RCP-'.uniqid(),
        'purchase_amount' => 10.00,
        'purchase_date' => Carbon::today()->toDateString(),
        'status' => 'submitted',
        'device_fingerprint' => null,
        'submitted_ip' => null,
    ], $overrides));
}

function createEntry(Campaign $campaign, User $user, Receipt $receipt, array $overrides = []): Entry
{
    return Entry::create(array_merge([
        'campaign_id' => $campaign->id,
        'receipt_id' => $receipt->id,
        'user_id' => $user->id,
        'status' => 'approved',
        'risk_score' => 0,
    ], $overrides));
}

it('returns approved when no fraud signals are triggered', function () {
    [$campaign, $rule] = createCampaignWithRule([
        'max_entries_per_day' => 100,
        'velocity_per_hour' => 100,
    ]);

    $user = User::factory()->create();
    $receipt = createReceipt($campaign, $user);

    $result = app(FraudScoringService::class)->evaluate($receipt, $rule, $user);

    expect($result['score'])->toBe(0.0)
        ->and($result['status'])->toBe('approved')
        ->and($result['signals'])->toBeArray()->toHaveCount(0);
});

it('increases risk score when user exceeds hourly velocity', function () {
    [$campaign, $rule] = createCampaignWithRule([
        'velocity_per_hour' => 1,
        'risk_score_flag_threshold' => 20,
    ]);

    $user = User::factory()->create();

    $existingReceipt = createReceipt($campaign, $user, ['status' => 'accepted']);
    createEntry($campaign, $user, $existingReceipt, ['risk_score' => 10]);

    $newReceipt = createReceipt($campaign, $user);

    $result = app(FraudScoringService::class)->evaluate($newReceipt, $rule, $user);

    expect($result['score'])->toBeGreaterThanOrEqual(20.0)
        ->and($result['status'])->toBe('flagged')
        ->and($result['signals'])->toHaveCount(1)
        ->and($result['signals'][0]['signal_type'])->toBe('velocity_limit_hour');
});

it('adds daily limit signal when user exceeds daily entries', function () {
    [$campaign, $rule] = createCampaignWithRule([
        'max_entries_per_day' => 1,
        'velocity_per_hour' => 999,
        'risk_score_flag_threshold' => 20,
    ]);

    $user = User::factory()->create();
    $existingReceipt = createReceipt($campaign, $user, ['status' => 'accepted']);
    $existingEntry = createEntry($campaign, $user, $existingReceipt);
    $existingEntry->update(['created_at' => now()->subHours(2)]);

    $newReceipt = createReceipt($campaign, $user);

    $result = app(FraudScoringService::class)->evaluate($newReceipt, $rule, $user);

    expect($result['score'])->toBeGreaterThanOrEqual(25.0)
        ->and($result['status'])->toBe('flagged')
        ->and($result['signals'])->toHaveCount(1)
        ->and($result['signals'][0]['signal_type'])->toBe('daily_limit_reached');
});

it('adds shared ip and device fingerprint signals', function () {
    [$campaign, $rule] = createCampaignWithRule([
        'max_entries_per_day' => 999,
        'velocity_per_hour' => 999,
        'risk_score_flag_threshold' => 30,
    ]);

    $user = User::factory()->create();
    $sharedIp = '10.10.10.10';
    $fingerprint = 'device-abc';

    for ($i = 0; $i < 20; $i++) {
        createReceipt($campaign, $user, [
            'submitted_ip' => $sharedIp,
            'device_fingerprint' => $fingerprint,
            'receipt_number' => "RCP-IP-{$i}",
            'purchase_amount' => 20 + $i,
        ]);
    }

    for ($i = 0; $i < 5; $i++) {
        createReceipt($campaign, $user, [
            'submitted_ip' => '192.168.0.'.$i,
            'device_fingerprint' => $fingerprint,
            'receipt_number' => "RCP-FP-{$i}",
            'purchase_amount' => 200 + $i,
        ]);
    }

    $newReceipt = createReceipt($campaign, $user, [
        'submitted_ip' => $sharedIp,
        'device_fingerprint' => $fingerprint,
    ]);

    $result = app(FraudScoringService::class)->evaluate($newReceipt, $rule, $user);

    expect($result['score'])->toBeGreaterThanOrEqual(40.0)
        ->and($result['status'])->toBe('flagged')
        ->and($result['signals'])->toHaveCount(2)
        ->and($result['signals'][0]['signal_type'])->toBe('shared_ip_spike')
        ->and($result['signals'][1]['signal_type'])->toBe('device_fingerprint_spike');
});

it('marks entry as rejected when reject threshold is reached', function () {
    [$campaign, $rule] = createCampaignWithRule([
        'max_entries_per_day' => 1,
        'velocity_per_hour' => 1,
        'risk_score_flag_threshold' => 20,
        'risk_score_reject_threshold' => 60,
    ]);

    $user = User::factory()->create();
    $sharedIp = '172.16.1.1';
    $fingerprint = 'device-zx';

    $existingReceipt = createReceipt($campaign, $user, [
        'status' => 'accepted',
        'submitted_ip' => $sharedIp,
        'device_fingerprint' => $fingerprint,
    ]);
    createEntry($campaign, $user, $existingReceipt);

    for ($i = 0; $i < 20; $i++) {
        createReceipt($campaign, $user, [
            'submitted_ip' => $sharedIp,
            'device_fingerprint' => "other-device-{$i}",
            'receipt_number' => "RCP-IP-ONLY-{$i}",
            'purchase_amount' => 100 + $i,
        ]);
    }

    for ($i = 0; $i < 5; $i++) {
        createReceipt($campaign, $user, [
            'submitted_ip' => "172.16.2.{$i}",
            'device_fingerprint' => $fingerprint,
            'receipt_number' => "RCP-FP-ONLY-{$i}",
            'purchase_amount' => 150 + $i,
        ]);
    }

    $newReceipt = createReceipt($campaign, $user, [
        'submitted_ip' => $sharedIp,
        'device_fingerprint' => $fingerprint,
    ]);

    $result = app(FraudScoringService::class)->evaluate($newReceipt, $rule, $user);

    expect($result['score'])->toBeGreaterThanOrEqual(60.0)
        ->and($result['status'])->toBe('rejected')
        ->and($result['signals'])->toHaveCount(4);
});
