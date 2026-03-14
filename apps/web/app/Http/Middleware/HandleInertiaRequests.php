<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /** @return array<string, mixed> */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->values(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                ] : null,
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'legal' => [
                'brand' => config('legal.brand'),
                'organization_name' => config('legal.organization_name'),
                'support_email' => config('legal.support_email'),
                'privacy_email' => config('legal.privacy_email'),
                'complaints_email' => config('legal.complaints_email'),
                'support_phone' => config('legal.support_phone'),
                'support_hours' => config('legal.support_hours'),
                'privacy_last_updated' => config('legal.privacy_last_updated'),
                'terms_version' => config('legal.terms_version'),
                'terms_effective_at' => config('legal.terms_effective_at'),
            ],
            'ziggy' => fn () => array_merge((new Ziggy)->toArray(), [
                'location' => $request->url(),
            ]),
        ]);
    }
}
