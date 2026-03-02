<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WinnerResource;
use App\Models\Winner;

class PublicWinnerController extends Controller
{
    public function index()
    {
        return WinnerResource::collection(
            Winner::query()
                ->with(['user.profile', 'prize', 'campaign'])
                ->latest('published_at')
                ->paginate(100),
        );
    }
}
