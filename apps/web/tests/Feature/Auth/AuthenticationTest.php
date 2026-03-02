<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'fax_number' => '',
        '_form_started_at' => (int) floor(microtime(true) * 1000) - 5000,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
        'fax_number' => '',
        '_form_started_at' => (int) floor(microtime(true) * 1000) - 5000,
    ]);

    $this->assertGuest();
});

test('login request is blocked when honeypot trap is filled', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'fax_number' => 'bot-content',
        '_form_started_at' => (int) floor(microtime(true) * 1000) - 5000,
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
