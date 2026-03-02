<?php

namespace App\Events;

use App\Models\Winner;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WinnerPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Winner $winner) {}
}
