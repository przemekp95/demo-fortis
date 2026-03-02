<?php

namespace App\Models;

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

    public function rule(): HasOne
    {
        return $this->hasOne(CampaignRule::class);
    }

    public function prizes(): HasMany
    {
        return $this->hasMany(Prize::class);
    }

    public function drawSchedules(): HasMany
    {
        return $this->hasMany(DrawSchedule::class);
    }

    public function drawRuns(): HasMany
    {
        return $this->hasMany(DrawRun::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
