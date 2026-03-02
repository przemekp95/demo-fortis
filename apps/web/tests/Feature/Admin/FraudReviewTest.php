<?php

use App\Models\Campaign;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\User;
use Spatie\Permission\Models\Role;

it('allows admin to review flagged entry', function () {
    $campaign = Campaign::create([
        'name' => 'Test Campaign',
        'slug' => 'test-campaign-admin',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $admin = User::factory()->create(['email_verified_at' => now()]);
    Role::findOrCreate('admin');
    $admin->assignRole('admin');

    $participant = User::factory()->create();
    $receipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $participant->id,
        'receipt_number' => 'FRAUD-01',
        'purchase_amount' => 10,
        'purchase_date' => now()->toDateString(),
        'status' => 'flagged',
    ]);

    $entry = Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $receipt->id,
        'user_id' => $participant->id,
        'status' => 'flagged',
        'risk_score' => 95,
        'flagged_at' => now(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.fraud.review', $entry), [
            'decision' => 'approved',
            'reason' => 'Verified manually',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('fraud_reviews', [
        'entry_id' => $entry->id,
        'decision' => 'approved',
    ]);

    $this->assertDatabaseHas('entries', [
        'id' => $entry->id,
        'status' => 'approved',
    ]);
});
