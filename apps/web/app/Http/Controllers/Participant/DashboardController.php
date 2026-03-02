<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();
        $activeCampaign = Campaign::query()->active()->first();

        return Inertia::render('Participant/Dashboard', [
            'campaign' => $activeCampaign,
            'stats' => [
                'entries_total' => $user?->entries()->count() ?? 0,
                'entries_flagged' => $user?->entries()->where('status', 'flagged')->count() ?? 0,
                'winners_total' => $user?->winners()->count() ?? 0,
            ],
        ]);
    }
}
