<?php

use App\Models\Campaign;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\User;
use App\Services\Kpi\KpiService;
use Spatie\Permission\Models\Role;

it('persists KPI snapshots on mysql for the active campaign', function () {
    if (config('database.default') !== 'mysql') {
        $this->markTestSkipped('This prod-like test requires the mysql connection.');
    }

    $campaign = Campaign::create([
        'name' => 'Prod Like KPI Campaign',
        'slug' => 'prod-like-kpi-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $user = User::factory()->create(['email_verified_at' => now()]);
    $receipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'KPI-MYSQL-001',
        'purchase_amount' => 19.90,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $receipt->id,
        'user_id' => $user->id,
        'status' => 'approved',
        'risk_score' => 10,
        'approved_at' => now(),
    ]);

    $snapshot = app(KpiService::class)->snapshot();

    expect($snapshot->campaign_id)->toBe($campaign->id)
        ->and($snapshot->metrics['entries_total'])->toBe(1);

    $this->assertDatabaseHas('kpi_snapshots', [
        'campaign_id' => $campaign->id,
    ]);
});

it('records audit logs for admin write actions on mysql', function () {
    if (config('database.default') !== 'mysql') {
        $this->markTestSkipped('This prod-like test requires the mysql connection.');
    }

    $campaign = Campaign::create([
        'name' => 'Prod Like Audit Campaign',
        'slug' => 'prod-like-audit-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $admin = User::factory()->create(['email_verified_at' => now()]);
    Role::findOrCreate('admin');
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->post(route('admin.draws.run-now'), [
            'campaign_id' => $campaign->id,
            'type' => 'daily',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'action' => 'admin.draws.run-now',
    ]);
});
