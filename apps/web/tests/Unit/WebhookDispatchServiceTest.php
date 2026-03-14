<?php

use App\Jobs\DispatchWebhookJob;
use App\Models\ApiClient;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Services\Webhooks\WebhookDispatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('dispatches deliveries only to matching active endpoints', function () {
    Queue::fake();

    $matchingEndpoint = createWebhookEndpoint([
        'name' => 'Matching endpoint',
        'url' => 'https://example.test/hooks/matching',
        'events' => ['winner.published'],
    ]);

    createWebhookEndpoint([
        'name' => 'Different event',
        'url' => 'https://example.test/hooks/other',
        'events' => ['draw.completed'],
    ]);

    createWebhookEndpoint([
        'name' => 'Inactive endpoint',
        'url' => 'https://example.test/hooks/inactive',
        'events' => ['winner.published'],
        'is_active' => false,
    ]);

    app(WebhookDispatchService::class)->dispatch(
        eventType: 'winner.published',
        eventId: 'evt-1',
        campaignId: 12,
        payload: ['winner_id' => 55],
    );

    $delivery = WebhookDelivery::query()->sole();

    expect($delivery->webhook_endpoint_id)->toBe($matchingEndpoint->id)
        ->and($delivery->event_type)->toBe('winner.published')
        ->and($delivery->signature)->not->toBeEmpty();

    Queue::assertPushed(DispatchWebhookJob::class, function (DispatchWebhookJob $job) use ($delivery): bool {
        return $job->deliveryId === $delivery->id;
    });
});

it('marks a webhook delivery as successful on a 2xx response', function () {
    $endpoint = createWebhookEndpoint([
        'url' => 'https://example.test/hooks/success',
        'retry_limit' => 3,
        'timeout_seconds' => 5,
    ]);

    $delivery = WebhookDelivery::create([
        'webhook_endpoint_id' => $endpoint->id,
        'event_id' => 'evt-success',
        'event_type' => 'winner.published',
        'payload' => ['event_id' => 'evt-success', 'payload' => ['winner_id' => 1]],
        'signature' => 'signed-payload',
        'status' => 'pending',
        'attempt' => 0,
    ]);

    Http::fake([
        $endpoint->url => Http::response('accepted', 202),
    ]);

    app(WebhookDispatchService::class)->deliver($delivery);

    $delivery->refresh();

    expect($delivery->status)->toBe('success')
        ->and($delivery->attempt)->toBe(1)
        ->and($delivery->response_code)->toBe(202)
        ->and($delivery->response_body)->toBe('accepted')
        ->and($delivery->next_attempt_at)->toBeNull();

    Http::assertSent(function (Request $request) use ($endpoint): bool {
        return $request->url() === $endpoint->url
            && $request->hasHeader('X-Webhook-Event', 'winner.published')
            && $request->hasHeader('X-Webhook-Event-Id', 'evt-success')
            && $request->hasHeader('X-Webhook-Schema-Version', 'v1')
            && $request->hasHeader('X-Webhook-Signature', 'signed-payload');
    });
});

it('retries failed webhooks until the retry limit is exhausted', function () {
    Queue::fake();

    $endpoint = createWebhookEndpoint([
        'url' => 'https://example.test/hooks/fail',
        'retry_limit' => 2,
    ]);

    $delivery = WebhookDelivery::create([
        'webhook_endpoint_id' => $endpoint->id,
        'event_id' => 'evt-fail',
        'event_type' => 'draw.completed',
        'payload' => ['event_id' => 'evt-fail', 'payload' => ['draw_run_id' => 11]],
        'signature' => 'signed-failure',
        'status' => 'pending',
        'attempt' => 0,
    ]);

    Http::fake([
        $endpoint->url => Http::response('upstream failed', 500),
    ]);

    $service = app(WebhookDispatchService::class);
    $service->deliver($delivery);

    $delivery->refresh();

    expect($delivery->status)->toBe('pending')
        ->and($delivery->attempt)->toBe(1)
        ->and($delivery->response_code)->toBe(500)
        ->and($delivery->next_attempt_at)->not->toBeNull();

    Queue::assertPushed(DispatchWebhookJob::class, 1);

    Queue::fake();
    $service->deliver($delivery->fresh());

    $delivery->refresh();

    expect($delivery->status)->toBe('failed')
        ->and($delivery->attempt)->toBe(2)
        ->and($delivery->response_code)->toBe(500)
        ->and($delivery->next_attempt_at)->toBeNull();

    Queue::assertNothingPushed();
});

function createWebhookEndpoint(array $overrides = []): WebhookEndpoint
{
    $client = ApiClient::create([
        'name' => 'Webhook Client '.uniqid(),
        'slug' => 'webhook-client-'.uniqid(),
        'secret_hash' => hash('sha256', 'secret'),
        'rate_limit_per_minute' => 120,
        'is_active' => true,
    ]);

    return WebhookEndpoint::create(array_merge([
        'api_client_id' => $client->id,
        'name' => 'Default endpoint',
        'url' => 'https://example.test/hooks/default',
        'secret' => 'webhook-secret',
        'events' => ['winner.published'],
        'retry_limit' => 3,
        'timeout_seconds' => 10,
        'is_active' => true,
    ], $overrides));
}
