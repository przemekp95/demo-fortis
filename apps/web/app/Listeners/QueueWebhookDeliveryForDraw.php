<?php

namespace App\Listeners;

use App\Events\DrawCompleted;
use App\Services\Webhooks\WebhookDispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class QueueWebhookDeliveryForDraw implements ShouldQueue
{
    public function __construct(private readonly WebhookDispatchService $dispatchService) {}

    public function handle(DrawCompleted $event): void
    {
        $this->dispatchService->dispatch(
            'draw.completed',
            (string) Str::uuid(),
            $event->drawRun->campaign_id,
            [
                'draw_run_id' => $event->drawRun->id,
                'type' => $event->drawRun->type,
                'winner_count' => $event->drawRun->winners()->count(),
            ],
        );
    }
}
