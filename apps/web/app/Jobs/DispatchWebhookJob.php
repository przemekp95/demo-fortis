<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use App\Services\Webhooks\WebhookDispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly int $deliveryId) {}

    public function handle(WebhookDispatchService $dispatchService): void
    {
        $delivery = WebhookDelivery::query()->find($this->deliveryId);
        if ($delivery === null || $delivery->status === 'success') {
            return;
        }

        $dispatchService->deliver($delivery);
    }
}
