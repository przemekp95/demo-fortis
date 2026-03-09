<?php

use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\Campaign;
use App\Models\DrawRun;
use App\Models\Entry;
use App\Models\Prize;
use App\Models\Receipt;
use App\Models\User;
use App\Models\Winner;

it('returns current campaign and prize resources in documented envelopes', function () {
    $token = createPublicApiToken();

    $campaign = Campaign::create([
        'name' => 'Public Campaign',
        'slug' => 'public-campaign',
        'status' => 'active',
        'description' => 'Current public campaign',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $campaign->prizes()->create([
        'name' => 'Voucher 100 PLN',
        'draw_type' => 'daily',
        'quantity' => 5,
        'value' => 100,
    ]);

    $this
        ->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/campaign/current')
        ->assertOk()
        ->assertJsonPath('data.slug', 'public-campaign')
        ->assertJsonPath('data.description', 'Current public campaign');

    $this
        ->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/campaign/current/prizes')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Voucher 100 PLN')
        ->assertJsonPath('data.0.draw_type', 'daily');
});

it('returns paginated draw history with finished timestamps and winner counts', function () {
    $token = createPublicApiToken();

    $campaign = Campaign::create([
        'name' => 'Draw Campaign',
        'slug' => 'draw-campaign',
        'status' => 'active',
        'starts_at' => now()->subDays(3),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $drawRun = DrawRun::create([
        'campaign_id' => $campaign->id,
        'type' => 'daily',
        'idempotency_key' => 'draw-history-test',
        'status' => 'completed',
        'started_at' => now()->subHour(),
        'finished_at' => now()->subMinutes(30),
    ]);

    $prize = Prize::create([
        'campaign_id' => $campaign->id,
        'name' => 'Daily Prize',
        'draw_type' => 'daily',
        'quantity' => 1,
        'value' => 100,
    ]);

    $user = User::factory()->create();
    $receipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'DRAW-001',
        'purchase_amount' => 12.50,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    $entry = Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $receipt->id,
        'user_id' => $user->id,
        'status' => 'winner',
        'risk_score' => 5,
    ]);

    Winner::create([
        'campaign_id' => $campaign->id,
        'draw_run_id' => $drawRun->id,
        'prize_id' => $prize->id,
        'entry_id' => $entry->id,
        'user_id' => $user->id,
        'status' => 'notified',
        'published_at' => now()->subMinutes(20),
    ]);

    $this
        ->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/draws/history')
        ->assertOk()
        ->assertJsonPath('data.0.id', $drawRun->id)
        ->assertJsonPath('data.0.winner_count', 1)
        ->assertJsonStructure([
            'data' => [['id', 'campaign_id', 'type', 'status', 'started_at', 'finished_at', 'winner_count']],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);
});

it('returns only published winners in paginated public format', function () {
    $token = createPublicApiToken();

    $campaign = Campaign::create([
        'name' => 'Winners Campaign',
        'slug' => 'winners-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $drawRun = DrawRun::create([
        'campaign_id' => $campaign->id,
        'type' => 'daily',
        'idempotency_key' => 'winner-history-test',
        'status' => 'completed',
        'started_at' => now()->subHour(),
        'finished_at' => now()->subMinutes(30),
    ]);

    $prize = Prize::create([
        'campaign_id' => $campaign->id,
        'name' => 'Prize Name',
        'draw_type' => 'daily',
        'quantity' => 2,
        'value' => 50,
    ]);

    $publishedUser = User::factory()->create(['email' => 'winner@example.test']);
    $publishedUser->profile()->create([
        'first_name' => 'Anna',
        'last_name' => 'Winner',
        'phone' => '+48111111111',
        'address_line_1' => 'Street 1',
        'city' => 'Gdansk',
        'postal_code' => '80-001',
        'country' => 'PL',
        'birth_date' => '1990-01-01',
    ]);

    $publishedReceipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $publishedUser->id,
        'receipt_number' => 'WIN-001',
        'purchase_amount' => 19.99,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    $publishedEntry = Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $publishedReceipt->id,
        'user_id' => $publishedUser->id,
        'status' => 'winner',
        'risk_score' => 10,
    ]);

    Winner::create([
        'campaign_id' => $campaign->id,
        'draw_run_id' => $drawRun->id,
        'prize_id' => $prize->id,
        'entry_id' => $publishedEntry->id,
        'user_id' => $publishedUser->id,
        'status' => 'notified',
        'published_at' => now()->subMinutes(5),
    ]);

    $draftUser = User::factory()->create(['email' => 'draft@example.test']);
    $draftReceipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $draftUser->id,
        'receipt_number' => 'WIN-002',
        'purchase_amount' => 29.99,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    $draftEntry = Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $draftReceipt->id,
        'user_id' => $draftUser->id,
        'status' => 'winner',
        'risk_score' => 12,
    ]);

    Winner::create([
        'campaign_id' => $campaign->id,
        'draw_run_id' => $drawRun->id,
        'prize_id' => $prize->id,
        'entry_id' => $draftEntry->id,
        'user_id' => $draftUser->id,
        'status' => 'pending_contact',
        'published_at' => null,
    ]);

    $this
        ->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/winners')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.draw_run_id', $drawRun->id)
        ->assertJsonPath('data.0.entry_id', $publishedEntry->id)
        ->assertJsonPath('data.0.prize_name', 'Prize Name')
        ->assertJsonPath('data.0.winner.email', 'w***@example.test')
        ->assertJsonPath('data.0.winner.city', 'Gdansk');
});

it('returns public stats in a single resource envelope', function () {
    $token = createPublicApiToken();

    Campaign::create([
        'name' => 'Stats Campaign',
        'slug' => 'stats-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $this
        ->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/stats/public')
        ->assertOk()
        ->assertJsonPath('data.entries_total', 0)
        ->assertJsonPath('data.winners_total', 0)
        ->assertJsonPath('data.active_campaigns', 1);
});

function createPublicApiToken(): string
{
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

    return $plainToken;
}
