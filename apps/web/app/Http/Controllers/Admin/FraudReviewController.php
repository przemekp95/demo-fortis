<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewFraudRequest;
use App\Models\Entry;
use App\Models\FraudReview;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FraudReviewController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/FraudReviews/Index', [
            'entries' => Entry::query()
                ->where('status', 'flagged')
                ->with(['user.profile', 'receipt', 'fraudSignals'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function review(ReviewFraudRequest $request, Entry $entry): RedirectResponse
    {
        $decision = $request->validated('decision');

        FraudReview::create([
            'entry_id' => $entry->id,
            'reviewed_by' => $request->user()->id,
            'decision' => $decision,
            'reason' => $request->validated('reason'),
            'reviewed_at' => now(),
        ]);

        $entry->update([
            'status' => $decision === 'approved' ? 'approved' : 'rejected',
            'approved_at' => $decision === 'approved' ? now() : null,
            'rejected_at' => $decision !== 'approved' ? now() : null,
        ]);

        return back()->with('status', 'Ocena fraud zaktualizowana.');
    }
}
