<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraudSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'signal_type',
        'score',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'score' => 'float',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }
}
