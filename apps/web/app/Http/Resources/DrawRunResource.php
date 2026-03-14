<?php

namespace App\Http\Resources;

use App\Models\DrawRun;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DrawRun */
class DrawRunResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        $winnerCount = $this->resource->getAttribute('winners_count');

        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'type' => $this->type,
            'status' => $this->status,
            'started_at' => optional($this->started_at)->toIso8601String(),
            'finished_at' => optional($this->finished_at)->toIso8601String(),
            'winner_count' => $winnerCount === null ? $this->winners()->count() : (int) $winnerCount,
        ];
    }
}
