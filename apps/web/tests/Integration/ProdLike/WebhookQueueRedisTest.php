<?php

use App\Jobs\DispatchWebhookJob;
use App\Models\ApiClient;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Services\Webhooks\WebhookDispatchService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

beforeEach(function () {
    if (config('queue.default') !== 'redis') {
        $this->markTestSkipped('This prod-like test requires the redis queue connection.');
    }

    Redis::connection()->flushdb();
    config([
        'queue.default' => 'redis',
        'cache.default' => 'redis',
    ]);
});

afterEach(function () {
    if (config('queue.default') === 'redis') {
        Redis::connection()->flushdb();
    }
});

it('processes a successful webhook through the redis queue worker', function () {
    $endpoint = createRedisWebhookEndpoint([
        'url' => 'https://example.test/hooks/success',
        'retry_limit' => 2,
    ]);

    Http::fake([
        $endpoint->url => Http::response('ok', 200),
    ]);

    app(WebhookDispatchService::class)->dispatch('winner.published', 'evt-redis-success', 1, ['winner_id' => 44]);

    expect(WebhookDelivery::query()->count())->toBe(1)
        ->and(queuedJobsCount())->toBe(1);

    Artisan::call('queue:work', [
        'connection' => 'redis',
        '--once' => true,
        '--queue' => 'default',
        '--tries' => 1,
    ]);

    $delivery = WebhookDelivery::query()->sole();

    expect($delivery->fresh()->status)->toBe('success')
        ->and($delivery->attempt)->toBe(1)
        ->and($delivery->response_code)->toBe(200);

    Http::assertSentCount(1);
});

it('schedules a retry on redis when webhook delivery fails', function () {
    $endpoint = createRedisWebhookEndpoint([
        'url' => 'https://example.test/hooks/retry',
        'retry_limit' => 2,
    ]);

    Http::fake([
        $endpoint->url => Http::response('retry later', 500),
    ]);

    app(WebhookDispatchService::class)->dispatch('draw.completed', 'evt-redis-retry', 1, ['draw_run_id' => 77]);

    Artisan::call('queue:work', [
        'connection' => 'redis',
        '--once' => true,
        '--queue' => 'default',
        '--tries' => 1,
    ]);

    $delivery = WebhookDelivery::query()->sole()->fresh();

    expect($delivery->status)->toBe('pending')
        ->and($delivery->attempt)->toBe(1)
        ->and($delivery->next_attempt_at)->not->toBeNull()
        ->and(delayedJobsCount())->toBe(1);
});

it('marks the webhook delivery as failed when the retry limit is exhausted on redis', function () {
    $endpoint = createRedisWebhookEndpoint([
        'url' => 'https://example.test/hooks/final-failure',
        'retry_limit' => 1,
    ]);

    $delivery = WebhookDelivery::create([
        'webhook_endpoint_id' => $endpoint->id,
        'event_id' => 'evt-redis-failure',
        'event_type' => 'winner.published',
        'payload' => ['event_id' => 'evt-redis-failure', 'payload' => ['winner_id' => 88]],
        'signature' => 'signed-final-failure',
        'status' => 'pending',
        'attempt' => 0,
    ]);

    Http::fake([
        $endpoint->url => Http::response('boom', 500),
    ]);

    DispatchWebhookJob::dispatch($delivery->id);

    Artisan::call('queue:work', [
        'connection' => 'redis',
        '--once' => true,
        '--queue' => 'default',
        '--tries' => 1,
    ]);

    $delivery->refresh();

    expect($delivery->status)->toBe('failed')
        ->and($delivery->attempt)->toBe(1)
        ->and($delivery->next_attempt_at)->toBeNull()
        ->and(delayedJobsCount())->toBe(0);
});

function createRedisWebhookEndpoint(array $overrides = []): WebhookEndpoint
{
    $client = ApiClient::create([
        'name' => 'Redis Webhook Client '.uniqid(),
        'slug' => 'redis-webhook-client-'.uniqid(),
        'secret_hash' => hash('sha256', 'secret'),
        'rate_limit_per_minute' => 120,
        'is_active' => true,
    ]);

    return WebhookEndpoint::create(array_merge([
        'api_client_id' => $client->id,
        'name' => 'Redis endpoint',
        'url' => 'https://example.test/hooks/default',
        'secret' => 'redis-webhook-secret',
        'events' => ['winner.published', 'draw.completed'],
        'retry_limit' => 2,
        'timeout_seconds' => 10,
        'is_active' => true,
    ], $overrides));
}

function queuedJobsCount(): int
{
    return (int) app('queue')->connection('redis')->size('default');
}

function delayedJobsCount(): int
{
    return (int) Redis::connection()->zcard('queues:default:delayed');
}
