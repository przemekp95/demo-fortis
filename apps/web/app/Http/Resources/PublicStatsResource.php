<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicStatsResource extends JsonResource
{
    /** @return array<string, int> */
    public function toArray($request): array
    {
        return [
            'entries_total' => $this['entries_total'] ?? 0,
            'winners_total' => $this['winners_total'] ?? 0,
            'active_campaigns' => $this['active_campaigns'] ?? 0,
        ];
    }
}
