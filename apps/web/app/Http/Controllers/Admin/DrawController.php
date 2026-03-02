<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\DrawSchedule;
use App\Services\Draw\DrawService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DrawController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Draws/Index', [
            'schedules' => DrawSchedule::query()->with('campaign')->latest('run_at')->paginate(30),
        ]);
    }

    public function runNow(Request $request, DrawService $drawService): RedirectResponse
    {
        $data = $request->validate([
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'type' => ['required', 'in:daily,final'],
        ]);

        $campaign = Campaign::query()->findOrFail($data['campaign_id']);
        $schedule = DrawSchedule::create([
            'campaign_id' => $campaign->id,
            'type' => $data['type'],
            'run_at' => now(),
            'status' => 'pending',
        ]);

        $drawService->runSchedule($schedule);

        return back()->with('status', 'Losowanie uruchomione.');
    }
}
