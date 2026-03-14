<?php

use App\Models\DsrRequest;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;

it('allows admins to see submitted dsr requests', function () {
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $participant = User::factory()->create(['email_verified_at' => now()]);

    Role::findOrCreate('admin');
    $admin->assignRole('admin');

    DsrRequest::create([
        'user_id' => $participant->id,
        'email' => $participant->email,
        'type' => 'export',
        'status' => 'requested',
        'requested_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dsr-requests.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/DsrRequests/Index')
            ->where('requests.data.0.email', $participant->email)
            ->where('requests.data.0.type', 'export'));
});
