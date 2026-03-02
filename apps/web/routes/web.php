<?php

use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DrawController;
use App\Http\Controllers\Admin\FraudReviewController;
use App\Http\Controllers\Admin\WinnerExportController;
use App\Http\Controllers\Participant\DashboardController as ParticipantDashboardController;
use App\Http\Controllers\Participant\DsrController;
use App\Http\Controllers\Participant\ReceiptController;
use App\Http\Controllers\Participant\WebPushSubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Models\Campaign;
use App\Models\DrawRun;
use App\Models\User;
use App\Models\Winner;
use App\Services\Kpi\KpiService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $campaign = Campaign::query()
        ->active()
        ->with(['rule', 'prizes'])
        ->first();

    $recentDraws = DrawRun::query()
        ->where('status', 'completed')
        ->withCount('winners')
        ->latest('started_at')
        ->limit(6)
        ->get()
        ->map(fn (DrawRun $drawRun): array => [
            'id' => $drawRun->id,
            'type' => $drawRun->type,
            'winner_count' => $drawRun->winners_count,
            'started_at' => optional($drawRun->started_at)->toIso8601String(),
            'finished_at' => optional($drawRun->finished_at)->toIso8601String(),
        ])
        ->values();

    $recentWinners = Winner::query()
        ->with(['user.profile', 'prize'])
        ->whereNotNull('published_at')
        ->latest('published_at')
        ->limit(6)
        ->get()
        ->map(function (Winner $winner): array {
            $email = $winner->user?->email;

            return [
                'id' => $winner->id,
                'prize' => $winner->prize?->name,
                'winner' => [
                    'email' => $email === null ? null : preg_replace('/(^.).+(@.+$)/', '$1***$2', $email),
                    'city' => $winner->user?->profile?->city,
                ],
                'published_at' => optional($winner->published_at)->toIso8601String(),
            ];
        })
        ->values();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'campaign' => $campaign ? [
            'name' => $campaign->name,
            'description' => $campaign->description,
            'starts_at' => optional($campaign->starts_at)->toIso8601String(),
            'ends_at' => optional($campaign->ends_at)->toIso8601String(),
            'final_draw_at' => optional($campaign->final_draw_at)->toIso8601String(),
            'terms_url' => $campaign->terms_url ?: route('terms'),
            'rule' => $campaign->rule ? [
                'max_entries_per_day' => $campaign->rule->max_entries_per_day,
                'min_purchase_amount' => (float) $campaign->rule->min_purchase_amount,
                'max_receipt_age_days' => $campaign->rule->max_receipt_age_days,
            ] : null,
            'prizes' => $campaign->prizes
                ->map(fn ($prize): array => [
                    'id' => $prize->id,
                    'name' => $prize->name,
                    'draw_type' => $prize->draw_type,
                    'quantity' => $prize->quantity,
                    'value' => $prize->value,
                ])
                ->values(),
        ] : null,
        'stats' => app(KpiService::class)->publicMetrics(),
        'recentDraws' => $recentDraws,
        'recentWinners' => $recentWinners,
    ]);
})->name('home');

Route::get('/polityka-prywatnosci', function () {
    return Inertia::render('Public/PrivacyPolicy');
})->name('privacy-policy');

Route::get('/regulamin', function () {
    return Inertia::render('Public/Terms');
})->name('terms');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user instanceof User && $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('participant.dashboard');
    })->name('dashboard');

    Route::prefix('participant')->as('participant.')->group(function (): void {
        Route::get('/dashboard', ParticipantDashboardController::class)->name('dashboard');
        Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
        Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
        Route::post('/web-push-subscriptions', [WebPushSubscriptionController::class, 'store'])->name('webpush.store');
        Route::post('/dsr', [DsrController::class, 'store'])->name('dsr.store');
    });

    Route::prefix('admin')->as('admin.')->middleware(['admin', 'audit'])->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
        Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
        Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
        Route::post('/campaigns/{campaign}/activate', [CampaignController::class, 'activate'])->name('campaigns.activate');

        Route::get('/draws', [DrawController::class, 'index'])->name('draws.index');
        Route::post('/draws/run-now', [DrawController::class, 'runNow'])->name('draws.run-now');

        Route::get('/fraud-reviews', [FraudReviewController::class, 'index'])->name('fraud.index');
        Route::post('/fraud-reviews/{entry}', [FraudReviewController::class, 'review'])->name('fraud.review');

        Route::get('/winner-exports', [WinnerExportController::class, 'index'])->name('winner-exports.index');
        Route::post('/campaigns/{campaign}/winner-exports', [WinnerExportController::class, 'store'])->name('winner-exports.store');
        Route::get('/winner-exports/{winnerExport}/download', [WinnerExportController::class, 'download'])->name('winner-exports.download');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
