<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use App\Models\ConsentVersion;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'consentVersions' => ConsentVersion::query()
                ->where('is_active', true)
                ->orderBy('code')
                ->get(['id', 'code', 'version', 'label']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32', 'unique:profiles,phone'],
            'birth_date' => ['required', 'date', 'before:-18 years'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:32'],
            'country' => ['required', 'string', 'size:2'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'accepted_consents' => ['required', 'array', 'min:1'],
            'accepted_consents.*' => ['integer', 'exists:consent_versions,id'],
            'device_fingerprint' => ['nullable', 'string', 'max:128'],
        ]);

        $user = DB::transaction(function () use ($validated, $request): User {
            $user = User::create([
                'name' => trim($validated['first_name'].' '.$validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->profile()->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'address_line_1' => $validated['address_line_1'],
                'address_line_2' => $validated['address_line_2'] ?? null,
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'country' => strtoupper($validated['country']),
                'birth_date' => $validated['birth_date'],
                'device_fingerprint' => $validated['device_fingerprint'] ?? null,
                'last_login_ip' => $request->ip(),
            ]);

            $consentRows = ConsentVersion::query()
                ->whereIn('id', $validated['accepted_consents'])
                ->where('is_active', true)
                ->pluck('id')
                ->map(fn (int $consentVersionId): array => [
                    'user_id' => $user->id,
                    'consent_version_id' => $consentVersionId,
                    'accepted_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->all();

            if (count($consentRows) === 0) {
                abort(422, 'Brak aktywnych zgód.');
            }

            Consent::insert($consentRows);

            Role::findOrCreate('participant');
            $user->assignRole('participant');

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
