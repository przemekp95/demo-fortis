<?php

namespace App\Http\Middleware;

use App\Support\AuditLogger;
use Closure;
use Illuminate\Http\Request;

class AuditRequest
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (
            $request->user() !== null
            && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && $request->route()?->getName() !== null
        ) {
            AuditLogger::log(
                action: $request->route()->getName(),
                auditable: null,
                metadata: [
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'status' => $response->getStatusCode(),
                ],
                userId: $request->user()->id,
                request: $request,
            );
        }

        return $response;
    }
}
