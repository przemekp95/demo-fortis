<?php

use App\Models\ConsentVersion;
use App\Providers\RouteServiceProvider;

test('registration screen can be rendered', function () {
    ConsentVersion::create([
        'code' => 'terms',
        'version' => 1,
        'label' => 'Regulamin',
        'content' => 'Treść',
        'is_active' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $consent = ConsentVersion::create([
        'code' => 'terms',
        'version' => 1,
        'label' => 'Regulamin',
        'content' => 'Treść',
        'is_active' => true,
        'published_at' => now(),
    ]);

    $response = $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '+48123123123',
        'birth_date' => '1990-01-01',
        'address_line_1' => 'Test Street 1',
        'city' => 'Warsaw',
        'postal_code' => '00-001',
        'country' => 'PL',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'accepted_consents' => [$consent->id],
        'fax_number' => '',
        '_form_started_at' => (int) floor(microtime(true) * 1000) - 5000,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('registration request is blocked when honeypot trap is filled', function () {
    $consent = ConsentVersion::create([
        'code' => 'terms',
        'version' => 1,
        'label' => 'Regulamin',
        'content' => 'Treść',
        'is_active' => true,
        'published_at' => now(),
    ]);

    $response = $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '+48123123123',
        'birth_date' => '1990-01-01',
        'address_line_1' => 'Test Street 1',
        'city' => 'Warsaw',
        'postal_code' => '00-001',
        'country' => 'PL',
        'email' => 'blocked@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'accepted_consents' => [$consent->id],
        'fax_number' => 'bot-content',
        '_form_started_at' => (int) floor(microtime(true) * 1000) - 5000,
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration endpoint is rate limited', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $response = $this->post('/register', [
            'fax_number' => '',
            '_form_started_at' => (int) floor(microtime(true) * 1000) - 5000,
        ]);

        $response->assertStatus(302);
    }

    $response = $this->post('/register', [
        'fax_number' => '',
        '_form_started_at' => (int) floor(microtime(true) * 1000) - 5000,
    ]);

    $response->assertStatus(429);
});
