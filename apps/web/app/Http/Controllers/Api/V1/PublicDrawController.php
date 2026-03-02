<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DrawRunResource;
use App\Models\DrawRun;

class PublicDrawController extends Controller
{
    public function history()
    {
        return DrawRunResource::collection(
            DrawRun::query()
                ->where('status', 'completed')
                ->latest('started_at')
                ->paginate(50),
        );
    }
}
