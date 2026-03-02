<?php

namespace App\Listeners;

use App\Events\WinnerPublished;
use App\Services\Webhooks\WebhookDispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class QueueWebhookDeliveryForWinner implements ShouldQueue
{
    public function __construct(private readonly WebhookDispatchService $dispatchService) {}

    public function handle(WinnerPublished $event): void
    {
        $this->dispatchService->dispatch(
            'winner.published',
            (string) Str::uuid(),
            $event->winner->campaign_id,
            [
                'winner_id' => $event->winner->id,
                'entry_id' => $event->winner->entry_id,
                'prize_id' => $event->winner->prize_id,
            ],
        );
    }
}
