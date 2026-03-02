<?php

namespace App\Services\Webhooks;

use App\Jobs\DispatchWebhookJob;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WebhookDispatchService
{
    public function __construct(private readonly WebhookSigningService $webhookSigningService) {}

    public function dispatch(string $eventType, string $eventId, int $campaignId, array $payload): void
    {
        $endpoints = WebhookEndpoint::query()->where('is_active', true)->get();

        if (DB::getDriverName() === 'sqlite') {
            $endpoints = $endpoints->filter(function (WebhookEndpoint $endpoint) use ($eventType): bool {
                $events = is_array($endpoint->events) ? $endpoint->events : [];

                return in_array($eventType, $events, true);
            });
        } else {
            $endpoints = WebhookEndpoint::query()
                ->where('is_active', true)
                ->whereJsonContains('events', $eventType)
                ->get();
        }

        $endpoints->each(function (WebhookEndpoint $endpoint) use ($eventType, $eventId, $campaignId, $payload): void {
            $envelope = [
                'event_id' => $eventId,
                'event_type' => $eventType,
                'occurred_at' => now()->toIso8601String(),
                'campaign_id' => $campaignId,
                'payload' => $payload,
            ];

            $signature = $this->webhookSigningService->sign($envelope, $endpoint->secret);

            $delivery = WebhookDelivery::create([
                'webhook_endpoint_id' => $endpoint->id,
                'event_id' => $eventId,
                'event_type' => $eventType,
                'payload' => $envelope,
                'signature' => $signature,
                'status' => 'pending',
                'attempt' => 0,
            ]);

            DispatchWebhookJob::dispatch($delivery->id);
        });
    }

    public function deliver(WebhookDelivery $delivery): void
    {
        $endpoint = $delivery->webhookEndpoint;

        $attempt = $delivery->attempt + 1;
        $delivery->update([
            'attempt' => $attempt,
            'last_attempt_at' => now(),
        ]);

        try {
            $response = Http::timeout($endpoint->timeout_seconds)
                ->withHeaders([
                    'X-Webhook-Event' => $delivery->event_type,
                    'X-Webhook-Event-Id' => $delivery->event_id,
                    'X-Webhook-Schema-Version' => 'v1',
                    'X-Webhook-Signature' => $delivery->signature,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint->url, $delivery->payload);

            if ($response->successful()) {
                $delivery->update([
                    'status' => 'success',
                    'response_code' => $response->status(),
                    'response_body' => mb_substr((string) $response->body(), 0, 65000),
                    'next_attempt_at' => null,
                ]);

                return;
            }

            $this->markFailedAttempt($delivery, $endpoint->retry_limit, $response->status(), $response->body());
        } catch (\Throwable $exception) {
            $this->markFailedAttempt($delivery, $endpoint->retry_limit, 0, $exception->getMessage());
        }
    }

    private function markFailedAttempt(WebhookDelivery $delivery, int $retryLimit, int $statusCode, string $body): void
    {
        $shouldRetry = $delivery->attempt < $retryLimit;

        $delivery->update([
            'status' => $shouldRetry ? 'pending' : 'failed',
            'response_code' => $statusCode ?: null,
            'response_body' => mb_substr($body, 0, 65000),
            'next_attempt_at' => $shouldRetry ? now()->addMinutes(min($delivery->attempt * 2, 30)) : null,
        ]);

        if ($shouldRetry) {
            DispatchWebhookJob::dispatch($delivery->id)->delay($delivery->next_attempt_at);
        }
    }
}
