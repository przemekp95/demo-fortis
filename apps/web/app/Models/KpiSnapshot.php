<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'bucket_at',
        'metrics',
    ];

    protected $casts = [
        'bucket_at' => 'datetime',
        'metrics' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
