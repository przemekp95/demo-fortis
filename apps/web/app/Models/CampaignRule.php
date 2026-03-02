<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'max_entries_per_day',
        'velocity_per_hour',
        'max_receipt_age_days',
        'min_purchase_amount',
        'deduplicate_receipts',
        'risk_score_flag_threshold',
        'risk_score_reject_threshold',
        'extra_rules',
    ];

    protected $casts = [
        'deduplicate_receipts' => 'boolean',
        'extra_rules' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
