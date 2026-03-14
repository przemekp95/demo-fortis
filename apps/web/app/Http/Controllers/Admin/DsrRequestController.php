<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DsrRequest;
use Inertia\Response;

class DsrRequestController extends Controller
{
    public function index(): Response
    {
        return inertia('Admin/DsrRequests/Index', [
            'requests' => DsrRequest::query()
                ->with('user.profile')
                ->latest('requested_at')
                ->paginate(20),
        ]);
    }
}
