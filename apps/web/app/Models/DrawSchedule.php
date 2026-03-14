<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrawSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'type',
        'run_at',
        'status',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'run_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /** @return BelongsTo<Campaign, self> */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
