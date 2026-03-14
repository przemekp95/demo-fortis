<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'webhook_endpoint_id',
        'event_id',
        'event_type',
        'payload',
        'signature',
        'status',
        'attempt',
        'last_attempt_at',
        'next_attempt_at',
        'response_code',
        'response_body',
    ];

    protected $casts = [
        'payload' => 'array',
        'last_attempt_at' => 'datetime',
        'next_attempt_at' => 'datetime',
    ];

    /** @return BelongsTo<WebhookEndpoint, self> */
    public function webhookEndpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class);
    }
}
