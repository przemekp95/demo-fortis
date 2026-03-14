<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiClientToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-API-Token');
        if ($token === null) {
            return response()->json(['message' => 'Missing API token'], 401);
        }

        $tokenHash = hash('sha256', $token);

        $apiToken = ApiToken::query()
            ->with('apiClient')
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($apiToken === null || ! $apiToken->apiClient?->is_active) {
            return response()->json(['message' => 'Invalid API token'], 401);
        }

        $rateLimit = (int) $apiToken->apiClient->rate_limit_per_minute;
        $bucket = now()->format('YmdHi');
        $cacheKey = sprintf('api-client:%d:%s', $apiToken->api_client_id, $bucket);
        $count = (int) Cache::get($cacheKey, 0);

        if ($count >= $rateLimit) {
            return response()->json(['message' => 'Rate limit exceeded'], 429);
        }

        Cache::put($cacheKey, $count + 1, now()->addMinutes(2));

        $apiToken->update(['last_used_at' => now()]);
        $apiToken->apiClient->update(['last_used_at' => now()]);

        $request->attributes->set('apiClient', $apiToken->apiClient);

        return $next($request);
    }
}
