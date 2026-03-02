<?php

namespace App\Events;

use App\Models\Entry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntryAccepted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Entry $entry) {}
}
