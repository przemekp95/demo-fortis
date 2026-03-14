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

    /** @return BelongsTo<Campaign, self> */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /** @return BelongsTo<Receipt, self> */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    /** @return BelongsTo<User, self> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<FraudSignal> */
    public function fraudSignals(): HasMany
    {
        return $this->hasMany(FraudSignal::class);
    }

    /** @return HasOne<FraudReview> */
    public function fraudReview(): HasOne
    {
        return $this->hasOne(FraudReview::class);
    }

    /** @return HasOne<Winner> */
    public function winner(): HasOne
    {
        return $this->hasOne(Winner::class);
    }
}
