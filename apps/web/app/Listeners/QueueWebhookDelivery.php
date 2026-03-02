<?php

namespace App\Listeners;

use App\Events\EntryAccepted;
use App\Services\Webhooks\WebhookDispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class QueueWebhookDelivery implements ShouldQueue
{
    public function __construct(private readonly WebhookDispatchService $dispatchService) {}

    public function handle(EntryAccepted $event): void
    {
        $this->dispatchService->dispatch(
            'entry.accepted',
            (string) Str::uuid(),
            $event->entry->campaign_id,
            [
                'entry_id' => $event->entry->id,
                'user_id' => $event->entry->user_id,
                'risk_score' => $event->entry->risk_score,
            ],
        );
    }
}
