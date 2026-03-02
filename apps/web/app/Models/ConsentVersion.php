<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'version',
        'label',
        'content',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }
}
