<?php

use App\Services\Webhooks\WebhookSigningService;

it('signs payload deterministically', function () {
    $service = app(WebhookSigningService::class);

    $payload = ['a' => 1, 'b' => 'x'];
    $signature1 = $service->sign($payload, 'secret');
    $signature2 = $service->sign($payload, 'secret');

    expect($signature1)->toBe($signature2)
        ->and(strlen($signature1))->toBe(64);
});
