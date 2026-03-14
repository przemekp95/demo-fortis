<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'description',
        'timezone',
        'starts_at',
        'ends_at',
        'final_draw_at',
        'terms_url',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'final_draw_at' => 'datetime',
    ];

    /** @return HasOne<CampaignRule> */
    public function rule(): HasOne
    {
        return $this->hasOne(CampaignRule::class);
    }

    /** @return HasMany<Prize> */
    public function prizes(): HasMany
    {
        return $this->hasMany(Prize::class);
    }

    /** @return HasMany<DrawSchedule> */
    public function drawSchedules(): HasMany
    {
        return $this->hasMany(DrawSchedule::class);
    }

    /** @return HasMany<DrawRun> */
    public function drawRuns(): HasMany
    {
        return $this->hasMany(DrawRun::class);
    }

    /** @return HasMany<Receipt> */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    /** @return HasMany<Entry> */
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    /** @return HasMany<Winner> */
    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    /** @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
