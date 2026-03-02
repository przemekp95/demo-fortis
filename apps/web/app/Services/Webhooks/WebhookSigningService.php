<?php

namespace App\Services\Webhooks;

class WebhookSigningService
{
    public function sign(array $payload, string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload, JSON_THROW_ON_ERROR), $secret);
    }
}
