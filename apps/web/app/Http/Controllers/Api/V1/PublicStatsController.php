<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicStatsResource;
use App\Services\Kpi\KpiService;

class PublicStatsController extends Controller
{
    public function __invoke(KpiService $kpiService): PublicStatsResource
    {
        return new PublicStatsResource($kpiService->publicMetrics());
    }
}
