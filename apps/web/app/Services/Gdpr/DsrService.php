<?php

namespace App\Services\Gdpr;

use App\Models\DsrRequest;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DsrService
{
    public function submit(User $user, string $type): DsrRequest
    {
        return DsrRequest::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'type' => $type,
            'status' => 'requested',
            'requested_at' => now(),
        ]);
    }

    public function process(DsrRequest $request): DsrRequest
    {
        $request->update(['status' => 'processing']);

        if ($request->type === 'export') {
            $payload = [
                'user' => $request->user?->only(['id', 'name', 'email']),
                'profile' => $request->user?->profile,
                'receipts' => $request->user?->receipts,
                'entries' => $request->user?->entries,
            ];

            $path = sprintf('exports/dsr-%d.json', $request->id);
            Storage::disk('local')->put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            $request->update([
                'status' => 'completed',
                'processed_at' => now(),
                'result_path' => $path,
            ]);

            return $request;
        }

        if ($request->user !== null && $request->type === 'delete') {
            $request->user->update([
                'name' => 'Anonymized User',
                'email' => sprintf('anonymized+%d@example.invalid', $request->user->id),
            ]);

            $request->user->profile()?->update([
                'first_name' => 'Anonymized',
                'last_name' => 'User',
                'phone' => sprintf('000000%06d', $request->user->id),
                'address_line_1' => 'Removed',
                'address_line_2' => null,
                'city' => 'Removed',
                'postal_code' => '00-000',
            ]);
        }

        $request->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        return $request;
    }
}
