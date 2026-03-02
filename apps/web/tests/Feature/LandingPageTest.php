<?php

use App\Models\Campaign;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

test('landing page exposes public campaign data', function () {
    $campaign = Campaign::query()->create([
        'name' => 'Fortis Loteria Test',
        'slug' => 'fortis-loteria-test',
        'status' => 'active',
        'description' => 'Testowa kampania landing page.',
        'timezone' => 'Europe/Warsaw',
        'starts_at' => Carbon::now()->subDay(),
        'ends_at' => Carbon::now()->addDays(7),
        'final_draw_at' => Carbon::now()->addDays(8),
        'terms_url' => 'https://fortis.example/terms',
    ]);

    $campaign->rule()->create([
        'max_entries_per_day' => 5,
        'velocity_per_hour' => 3,
        'max_receipt_age_days' => 14,
        'min_purchase_amount' => 1,
        'deduplicate_receipts' => true,
        'risk_score_flag_threshold' => 60,
        'risk_score_reject_threshold' => 90,
    ]);

    $campaign->prizes()->create([
        'name' => 'Voucher 50 PLN',
        'draw_type' => 'daily',
        'quantity' => 10,
        'value' => 50,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->where('campaign.name', 'Fortis Loteria Test')
        ->where('campaign.prizes.0.name', 'Voucher 50 PLN')
        ->where('stats.active_campaigns', 1)
        ->where('stats.entries_total', 0)
        ->where('stats.winners_total', 0)
        ->has('recentDraws')
        ->has('recentWinners'));
});
