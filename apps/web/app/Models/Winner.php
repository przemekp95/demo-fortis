<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'draw_run_id',
        'prize_id',
        'entry_id',
        'user_id',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /** @return BelongsTo<Campaign, self> */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /** @return BelongsTo<DrawRun, self> */
    public function drawRun(): BelongsTo
    {
        return $this->belongsTo(DrawRun::class);
    }

    /** @return BelongsTo<Prize, self> */
    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }

    /** @return BelongsTo<Entry, self> */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    /** @return BelongsTo<User, self> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
