<?php

use App\Models\Campaign;
use App\Models\DrawRun;
use App\Models\Entry;
use App\Models\Prize;
use App\Models\Receipt;
use App\Models\User;
use App\Models\Winner;
use App\Services\Reporting\WinnerExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('generates a winner export and persists the CSV in local storage', function () {
    Storage::fake('local');
    Carbon::setTestNow('2026-03-14 09:15:00');

    $campaign = Campaign::create([
        'name' => 'Export Campaign',
        'slug' => 'export-campaign',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'final_draw_at' => now()->addDays(2),
    ]);

    $generator = User::factory()->create(['email' => 'admin-export@example.test']);
    $winnerUser = User::factory()->create(['email' => 'winner-export@example.test']);
    $winnerUser->profile()->create([
        'first_name' => 'Maria',
        'last_name' => 'Nagroda',
        'phone' => '+48555000003',
        'address_line_1' => 'Winner Street 1',
        'city' => 'Lodz',
        'postal_code' => '90-001',
        'country' => 'PL',
        'birth_date' => '1991-01-01',
    ]);

    $drawRun = DrawRun::create([
        'campaign_id' => $campaign->id,
        'type' => 'daily',
        'idempotency_key' => 'winner-export-run',
        'status' => 'completed',
        'started_at' => now()->subHour(),
        'finished_at' => now()->subMinutes(30),
    ]);

    $prize = Prize::create([
        'campaign_id' => $campaign->id,
        'name' => 'Voucher 200 PLN',
        'draw_type' => 'daily',
        'quantity' => 1,
        'value' => 200,
    ]);

    $receipt = Receipt::create([
        'campaign_id' => $campaign->id,
        'user_id' => $winnerUser->id,
        'receipt_number' => 'EXPORT-001',
        'purchase_amount' => 49.99,
        'purchase_date' => now()->toDateString(),
        'status' => 'accepted',
    ]);

    $entry = Entry::create([
        'campaign_id' => $campaign->id,
        'receipt_id' => $receipt->id,
        'user_id' => $winnerUser->id,
        'status' => 'winner',
        'risk_score' => 5,
    ]);

    Winner::create([
        'campaign_id' => $campaign->id,
        'draw_run_id' => $drawRun->id,
        'prize_id' => $prize->id,
        'entry_id' => $entry->id,
        'user_id' => $winnerUser->id,
        'status' => 'notified',
        'published_at' => now()->subMinutes(10),
    ]);

    $export = app(WinnerExportService::class)->export($campaign, $generator);

    expect($export->row_count)->toBe(1)
        ->and($export->format)->toBe('csv')
        ->and($export->generated_by)->toBe($generator->id);

    Storage::disk('local')->assertExists($export->path);

    $csv = Storage::disk('local')->get($export->path);

    expect($csv)->toContain('winner_id,user_email,first_name,last_name,phone,prize,status,published_at')
        ->and($csv)->toContain('winner-export@example.test')
        ->and($csv)->toContain('Voucher 200 PLN');

    Carbon::setTestNow();
});
