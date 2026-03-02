<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataRetentionJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'target_date',
        'metadata',
        'ran_at',
        'error_message',
    ];

    protected $casts = [
        'target_date' => 'datetime',
        'ran_at' => 'datetime',
        'metadata' => 'array',
    ];
}
