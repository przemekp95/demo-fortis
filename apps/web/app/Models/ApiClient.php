<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'secret_hash',
        'rate_limit_per_minute',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /** @return HasMany<ApiToken> */
    public function tokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    /** @return HasMany<WebhookEndpoint> */
    public function webhookEndpoints(): HasMany
    {
        return $this->hasMany(WebhookEndpoint::class);
    }
}
