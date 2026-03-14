<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DrawRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'draw_schedule_id',
        'type',
        'idempotency_key',
        'status',
        'started_at',
        'finished_at',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'metadata' => 'array',
    ];

    /** @return BelongsTo<Campaign, self> */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /** @return BelongsTo<DrawSchedule, self> */
    public function drawSchedule(): BelongsTo
    {
        return $this->belongsTo(DrawSchedule::class);
    }

    /** @return HasMany<Winner> */
    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }
}
