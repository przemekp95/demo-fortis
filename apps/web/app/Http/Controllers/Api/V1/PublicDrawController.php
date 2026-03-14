<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DrawRunResource;
use App\Models\DrawRun;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicDrawController extends Controller
{
    public function history(): AnonymousResourceCollection
    {
        return DrawRunResource::collection(
            DrawRun::query()
                ->where('status', 'completed')
                ->withCount('winners')
                ->latest('started_at')
                ->paginate(50),
        );
    }
}
