<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WinnerResource;
use App\Models\Winner;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicWinnerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return WinnerResource::collection(
            Winner::query()
                ->with(['user.profile', 'prize', 'campaign'])
                ->whereNotNull('published_at')
                ->latest('published_at')
                ->paginate(100),
        );
    }
}
