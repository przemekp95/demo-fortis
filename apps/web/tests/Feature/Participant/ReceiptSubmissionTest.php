<?php

use App\Models\Campaign;
use App\Models\CampaignRule;
use App\Models\User;

it('submits receipt and creates entry for authenticated participant', function () {
    $campaign = Campaign::create([
        'name' => 'Test Campaign',
        'slug' => 'test-campaign-flow',
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

    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this
        ->actingAs($user)
        ->post(route('participant.receipts.store'), [
            'receipt_number' => 'TST-123',
            'purchase_amount' => 12.50,
            'purchase_date' => now()->toDateString(),
            'device_fingerprint' => 'device-1',
        ]);

    $response->assertRedirect(route('participant.receipts.index'));

    $this->assertDatabaseHas('receipts', [
        'receipt_number' => 'TST-123',
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('entries', [
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
    ]);
});
