<?php

use App\Models\Campaign;
use App\Models\User;
use Spatie\Permission\Models\Role;

it('records an audit log entry for admin write actions', function () {
    $campaign = Campaign::create([
        'name' => 'Audit Campaign',
        'slug' => 'audit-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $admin = User::factory()->create(['email_verified_at' => now()]);
    Role::findOrCreate('admin');
    $admin->assignRole('admin');

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.draws.run-now'), [
            'campaign_id' => $campaign->id,
            'type' => 'daily',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'action' => 'admin.draws.run-now',
        'auditable_type' => 'none',
        'auditable_id' => 0,
    ]);
});
