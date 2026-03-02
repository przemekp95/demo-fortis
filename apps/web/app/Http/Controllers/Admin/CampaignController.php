<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\DrawSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Campaigns/Index', [
            'campaigns' => Campaign::query()->with(['rule', 'prizes'])->latest()->get(),
        ]);
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $campaign = DB::transaction(function () use ($request): Campaign {
            if ($request->validated('status') === 'active') {
                Campaign::query()->where('status', 'active')->update(['status' => 'completed']);
            }

            $campaign = Campaign::create($request->safe()->except(['rule', 'prizes']));
            $campaign->rule()->create($request->validated('rule'));
            $campaign->prizes()->createMany($request->validated('prizes'));

            $this->generateSchedules($campaign);

            return $campaign;
        });

        return redirect()->route('admin.campaigns.index')->with('status', "Kampania {$campaign->name} utworzona.");
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        DB::transaction(function () use ($request, $campaign): void {
            if ($request->validated('status') === 'active' && $campaign->status !== 'active') {
                Campaign::query()->where('status', 'active')->where('id', '!=', $campaign->id)->update(['status' => 'completed']);
            }

            $campaign->update($request->validated());
        });

        return back()->with('status', 'Kampania zaktualizowana.');
    }

    public function activate(Campaign $campaign): RedirectResponse
    {
        DB::transaction(function () use ($campaign): void {
            Campaign::query()->where('status', 'active')->where('id', '!=', $campaign->id)->update(['status' => 'completed']);
            $campaign->update(['status' => 'active']);
        });

        return back()->with('status', "Kampania {$campaign->name} aktywna.");
    }

    private function generateSchedules(Campaign $campaign): void
    {
        $current = Carbon::parse($campaign->starts_at)->startOfDay();
        $end = Carbon::parse($campaign->ends_at)->startOfDay();

        while ($current->lte($end)) {
            DrawSchedule::firstOrCreate([
                'campaign_id' => $campaign->id,
                'type' => 'daily',
                'run_at' => $current->copy()->setTime(20, 0),
            ]);

            $current->addDay();
        }

        DrawSchedule::firstOrCreate([
            'campaign_id' => $campaign->id,
            'type' => 'final',
            'run_at' => Carbon::parse($campaign->final_draw_at),
        ]);
    }
}
