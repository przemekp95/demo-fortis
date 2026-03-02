<?php

use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\Campaign;

it('allows access to public api with valid token', function () {
    Campaign::create([
        'name' => 'Public Campaign',
        'slug' => 'public-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $plainToken = 'test-public-token';

    $client = ApiClient::create([
        'name' => 'Test Client',
        'slug' => 'test-client',
        'secret_hash' => hash('sha256', 'secret'),
        'rate_limit_per_minute' => 120,
        'is_active' => true,
    ]);

    ApiToken::create([
        'api_client_id' => $client->id,
        'token_hash' => hash('sha256', $plainToken),
        'expires_at' => now()->addDay(),
    ]);

    $response = $this
        ->withHeader('Authorization', 'Bearer '.$plainToken)
        ->getJson('/api/v1/campaign/current');

    $response->assertOk()->assertJsonPath('data.slug', 'public-campaign');
});
