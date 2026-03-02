<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\PrizeResource;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;

class PublicCampaignController extends Controller
{
    public function current(): CampaignResource|JsonResponse
    {
        $campaign = Campaign::query()->active()->first();
        if ($campaign === null) {
            return response()->json(['message' => 'No active campaign'], 404);
        }

        return new CampaignResource($campaign);
    }

    public function prizes(): JsonResponse
    {
        $campaign = Campaign::query()->active()->first();
        if ($campaign === null) {
            return response()->json(['message' => 'No active campaign'], 404);
        }

        return PrizeResource::collection($campaign->prizes()->get())->response();
    }
}
