<?php

use App\Models\Campaign;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\User;
use App\Services\Gdpr\DsrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('exports participant data and stores the generated file', function () {
    Storage::fake('local');

    $campaign = Campaign::create([
        'name' => 'DSR Campaign',
        'slug' => 'dsr-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $user = User::factory()->create(['email' => 'dsr@example.test']);
    $user->profile()->create([
        'first_name' => 'Alicja',
        'last_name' => 'Eksport',
        'phone' => '+48555000001',
        'address_line_1' => 'Export Street 1',
        'city' => 'Warszawa',
        'postal_code' => '00-101',
        'country' => 'PL',
        'birth_date' => '1995-05-05',
    ]);

    $receipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'receipt_number' => 'DSR-001',
        'purchase_amount' => 29.90,
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

    $request = app(DsrService::class)->submit($user, 'export');
    $processedRequest = app(DsrService::class)->process($request);

    $processedRequest->refresh();

    expect($processedRequest->status)->toBe('completed')
        ->and($processedRequest->result_path)->not->toBeNull()
        ->and($processedRequest->processed_at)->not->toBeNull();

    Storage::disk('local')->assertExists($processedRequest->result_path);

    $payload = json_decode(Storage::disk('local')->get($processedRequest->result_path), true, 512, JSON_THROW_ON_ERROR);

    expect($payload['user']['email'])->toBe('dsr@example.test')
        ->and($payload['profile']['first_name'])->toBe('Alicja')
        ->and($payload['receipts'][0]['receipt_number'])->toBe('DSR-001')
        ->and($payload['entries'][0]['status'])->toBe('approved');
});

it('anonymizes user data for delete requests', function () {
    $user = User::factory()->create(['email' => 'delete-me@example.test']);
    $user->profile()->create([
        'first_name' => 'Jan',
        'last_name' => 'Usuwany',
        'phone' => '+48555000002',
        'address_line_1' => 'Delete Street 1',
        'city' => 'Poznan',
        'postal_code' => '60-101',
        'country' => 'PL',
        'birth_date' => '1993-03-03',
    ]);

    $request = app(DsrService::class)->submit($user, 'delete');
    $processedRequest = app(DsrService::class)->process($request);

    $user->refresh();
    $user->profile->refresh();
    $processedRequest->refresh();

    expect($processedRequest->status)->toBe('completed')
        ->and($processedRequest->processed_at)->not->toBeNull()
        ->and($user->name)->toBe('Anonymized User')
        ->and($user->email)->toBe("anonymized+{$user->id}@example.invalid")
        ->and($user->profile->first_name)->toBe('Anonymized')
        ->and($user->profile->phone)->toBe(sprintf('000000%06d', $user->id));
});
