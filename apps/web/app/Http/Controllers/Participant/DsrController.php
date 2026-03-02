<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Services\Gdpr\DsrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DsrController extends Controller
{
    public function store(Request $request, DsrService $dsrService): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:export,delete'],
        ]);

        $dsrService->submit($request->user(), $data['type']);

        return back()->with('status', 'Wniosek RODO został przyjęty.');
    }
}
