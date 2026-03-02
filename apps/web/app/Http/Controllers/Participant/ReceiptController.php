<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitReceiptRequest;
use App\Models\Receipt;
use App\Services\Entries\EntrySubmissionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReceiptController extends Controller
{
    public function index(): Response
    {
        $receipts = Receipt::query()
            ->with('entry')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20)
            ->through(fn (Receipt $receipt) => [
                'id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'purchase_amount' => $receipt->purchase_amount,
                'purchase_date' => $receipt->purchase_date->toDateString(),
                'status' => $receipt->status,
                'entry_status' => $receipt->entry?->status,
                'risk_score' => $receipt->entry?->risk_score,
                'created_at' => $receipt->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Participant/Receipts/Index', [
            'receipts' => $receipts,
        ]);
    }

    public function store(SubmitReceiptRequest $request, EntrySubmissionService $entrySubmissionService): RedirectResponse
    {
        $entry = $entrySubmissionService->submit($request->user(), $request->validated(), $request);

        return redirect()
            ->route('participant.receipts.index')
            ->with('status', "Zgłoszenie zapisane. Status: {$entry->status}");
    }
}
