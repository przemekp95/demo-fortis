<?php

namespace App\Http\Resources;

use App\Models\Winner;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Winner */
class WinnerResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        $email = $this->user?->email;

        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'draw_run_id' => $this->draw_run_id,
            'entry_id' => $this->entry_id,
            'campaign_name' => $this->campaign?->name,
            'prize_name' => $this->prize?->name,
            'winner' => [
                'email' => $email === null ? null : preg_replace('/(^.).+(@.+$)/', '$1***$2', $email),
                'city' => $this->user?->profile?->city,
            ],
            'published_at' => optional($this->published_at)->toIso8601String(),
        ];
    }
}
