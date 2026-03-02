<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'receipt_id',
        'user_id',
        'status',
        'risk_score',
        'flagged_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'flagged_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'risk_score' => 'float',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fraudSignals(): HasMany
    {
        return $this->hasMany(FraudSignal::class);
    }

    public function fraudReview(): HasOne
    {
        return $this->hasOne(FraudReview::class);
    }

    public function winner(): HasOne
    {
        return $this->hasOne(Winner::class);
    }
}
