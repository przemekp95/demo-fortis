<?php

namespace App\Services\Webhooks;

class WebhookSigningService
{
    /** @param array<string, mixed> $payload */
    public function sign(array $payload, string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload, JSON_THROW_ON_ERROR), $secret);
    }
}
