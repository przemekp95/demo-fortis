<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->hasRole('admin')) {
            abort(403);
        }

        return $next($request);
    }
}
