<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HoneypotGuard
{
    private const TRAP_FIELD = 'fax_number';

    private const TIMESTAMP_FIELD = '_form_started_at';

    private const MIN_ELAPSED_MS = 1000;

    private const MAX_ELAPSED_MS = 7200000;

    public function handle(Request $request, Closure $next)
    {
        $trapValue = trim((string) $request->input(self::TRAP_FIELD, ''));
        $startedAt = $request->input(self::TIMESTAMP_FIELD);
        $elapsedMs = $this->elapsedMilliseconds($startedAt);

        if ($trapValue !== '' || $elapsedMs === null || $elapsedMs < self::MIN_ELAPSED_MS || $elapsedMs > self::MAX_ELAPSED_MS) {
            throw ValidationException::withMessages([
                'email' => 'Wykryto podejrzane zgłoszenie formularza.',
            ]);
        }

        return $next($request);
    }

    private function elapsedMilliseconds(mixed $submittedAt): ?int
    {
        if (! is_numeric($submittedAt)) {
            return null;
        }

        $submittedAtInt = (int) $submittedAt;
        if ($submittedAtInt <= 0) {
            return null;
        }

        // Accept both seconds and milliseconds.
        if ($submittedAtInt < 10000000000) {
            $submittedAtInt *= 1000;
        }

        return (int) floor(microtime(true) * 1000) - $submittedAtInt;
    }
}
