<?php

namespace App\Events;

use App\Models\DrawRun;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DrawCompleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly DrawRun $drawRun) {}
}
