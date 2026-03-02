<?php

namespace App\Services\Kpi;

use App\Models\Campaign;
use App\Models\Entry;
use App\Models\KpiSnapshot;
use App\Models\Winner;

class KpiService
{
    public function snapshot(): KpiSnapshot
    {
        $campaign = Campaign::query()->active()->first();

        $metrics = [
            'entries_total' => Entry::query()->when($campaign, fn ($q) => $q->where('campaign_id', $campaign->id))->count(),
            'entries_flagged' => Entry::query()->when($campaign, fn ($q) => $q->where('campaign_id', $campaign->id))->where('status', 'flagged')->count(),
            'entries_rejected' => Entry::query()->when($campaign, fn ($q) => $q->where('campaign_id', $campaign->id))->where('status', 'rejected')->count(),
            'winners_total' => Winner::query()->when($campaign, fn ($q) => $q->where('campaign_id', $campaign->id))->count(),
        ];

        return KpiSnapshot::updateOrCreate(
            [
                'campaign_id' => $campaign?->id,
                'bucket_at' => now()->startOfHour(),
            ],
            [
                'metrics' => $metrics,
            ],
        );
    }

    public function publicMetrics(): array
    {
        return [
            'entries_total' => Entry::query()->where('status', '!=', 'rejected')->count(),
            'winners_total' => Winner::query()->count(),
            'active_campaigns' => Campaign::query()->active()->count(),
        ];
    }
}
