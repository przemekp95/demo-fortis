<?php

namespace Database\Seeders;

use App\Models\ApiClient;
use App\Models\ApiToken;
use Illuminate\Database\Seeder;

class ApiClientSeeder extends Seeder
{
    public function run(): void
    {
        $plainToken = 'fortis-public-api-token';

        $client = ApiClient::updateOrCreate(
            ['slug' => 'public-web'],
            [
                'name' => 'Public Web Integrator',
                'secret_hash' => hash('sha256', 'public-web-secret'),
                'rate_limit_per_minute' => 300,
                'is_active' => true,
            ],
        );

        ApiToken::updateOrCreate(
            ['api_client_id' => $client->id, 'token_hash' => hash('sha256', $plainToken)],
            ['expires_at' => now()->addYear()],
        );

        $this->command?->warn('Sample public API token: '.$plainToken);
    }
}
