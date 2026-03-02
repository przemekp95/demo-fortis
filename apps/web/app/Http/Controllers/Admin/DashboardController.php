<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Kpi\KpiService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(KpiService $kpiService): Response
    {
        $snapshot = $kpiService->snapshot();

        return Inertia::render('Admin/Dashboard', [
            'snapshot' => [
                'bucket_at' => $snapshot->bucket_at->toDateTimeString(),
                'metrics' => $snapshot->metrics,
            ],
        ]);
    }
}
