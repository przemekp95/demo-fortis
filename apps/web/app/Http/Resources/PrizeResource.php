<?php

namespace App\Http\Resources;

use App\Models\Prize;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Prize */
class PrizeResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'draw_type' => $this->draw_type,
            'quantity' => $this->quantity,
            'value' => $this->value,
        ];
    }
}
