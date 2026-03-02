<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'receipt_number',
        'purchase_amount',
        'purchase_date',
        'device_fingerprint',
        'submitted_ip',
        'status',
        'validation_notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entry(): HasOne
    {
        return $this->hasOne(Entry::class);
    }
}
