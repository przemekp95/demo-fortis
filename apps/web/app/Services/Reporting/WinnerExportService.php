<?php

namespace App\Services\Reporting;

use App\Models\Campaign;
use App\Models\User;
use App\Models\WinnerExport;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class WinnerExportService
{
    public function export(Campaign $campaign, User $generatedBy): WinnerExport
    {
        $filename = sprintf('exports/winners-%d-%s.csv', $campaign->id, now()->format('YmdHis'));
        $handle = fopen('php://temp', 'w+b');

        if ($handle === false) {
            throw new RuntimeException('Unable to create winner export buffer.');
        }

        fputcsv($handle, ['winner_id', 'user_email', 'first_name', 'last_name', 'phone', 'prize', 'status', 'published_at']);

        $rowCount = 0;
        $campaign->winners()->with(['user.profile', 'prize'])->chunk(250, function ($winners) use ($handle, &$rowCount): void {
            foreach ($winners as $winner) {
                fputcsv($handle, [
                    $winner->id,
                    $winner->user->email,
                    $winner->user->profile?->first_name,
                    $winner->user->profile?->last_name,
                    $winner->user->profile?->phone,
                    $winner->prize?->name,
                    $winner->status,
                    optional($winner->published_at)->toDateTimeString(),
                ]);
                $rowCount++;
            }
        });

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        Storage::disk('local')->put($filename, $contents === false ? '' : $contents);

        return WinnerExport::create([
            'campaign_id' => $campaign->id,
            'generated_by' => $generatedBy->id,
            'format' => 'csv',
            'path' => $filename,
            'row_count' => $rowCount,
            'generated_at' => now(),
        ]);
    }
}
