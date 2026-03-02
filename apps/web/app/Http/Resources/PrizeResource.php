<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrizeResource extends JsonResource
{
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
