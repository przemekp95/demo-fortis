<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\User;
use App\Models\WinnerExport;
use App\Services\Reporting\WinnerExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WinnerExportController extends Controller
{
    public function index(): Response
    {
        return inertia('Admin/WinnerExports/Index', [
            'exports' => WinnerExport::query()->with(['campaign', 'generator'])->latest()->paginate(20),
        ]);
    }

    public function store(Campaign $campaign, WinnerExportService $winnerExportService): RedirectResponse
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $export = $winnerExportService->export($campaign, $user);

        return back()->with('status', "Eksport wygenerowany: {$export->path}");
    }

    public function download(WinnerExport $winnerExport): StreamedResponse
    {
        return Storage::disk('local')->download($winnerExport->path);
    }
}
