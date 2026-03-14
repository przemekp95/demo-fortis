<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'name',
        'draw_type',
        'quantity',
        'value',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /** @return BelongsTo<Campaign, self> */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /** @return HasMany<Winner> */
    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }
}
