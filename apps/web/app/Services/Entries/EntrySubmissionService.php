<?php

namespace App\Services\Entries;

use App\Events\EntryAccepted;
use App\Events\EntryFlagged;
use App\Models\Campaign;
use App\Models\Entry;
use App\Models\FraudSignal;
use App\Models\Receipt;
use App\Models\User;
use App\Services\Fraud\FraudScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EntrySubmissionService
{
    public function __construct(private readonly FraudScoringService $fraudScoringService) {}

    public function submit(User $user, array $payload, Request $request): Entry
    {
        $campaign = Campaign::query()
            ->active()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if ($campaign === null) {
            throw new RuntimeException('Brak aktywnej kampanii.');
        }

        $rule = $campaign->rule;
        if ($rule === null) {
            throw new RuntimeException('Brak skonfigurowanych zasad kampanii.');
        }

        $purchaseDate = Carbon::parse($payload['purchase_date']);
        if ($purchaseDate->lt(now()->subDays($rule->max_receipt_age_days)->startOfDay())) {
            throw new RuntimeException('Paragon jest zbyt stary.');
        }

        if ((float) $payload['purchase_amount'] < (float) $rule->min_purchase_amount) {
            throw new RuntimeException('Kwota zakupu jest poniżej minimalnej wartości.');
        }

        $entry = DB::transaction(function () use ($campaign, $rule, $user, $payload, $request): Entry {
            $receipt = Receipt::create([
                'campaign_id' => $campaign->id,
                'user_id' => $user->id,
                'receipt_number' => Arr::get($payload, 'receipt_number'),
                'purchase_amount' => Arr::get($payload, 'purchase_amount'),
                'purchase_date' => Arr::get($payload, 'purchase_date'),
                'device_fingerprint' => Arr::get($payload, 'device_fingerprint'),
                'submitted_ip' => $request->ip(),
                'status' => 'submitted',
            ]);

            $fraudResult = $this->fraudScoringService->evaluate($receipt, $rule, $user);

            $entry = Entry::create([
                'campaign_id' => $campaign->id,
                'receipt_id' => $receipt->id,
                'user_id' => $user->id,
                'status' => $fraudResult['status'] === 'approved' ? 'approved' : ($fraudResult['status'] === 'flagged' ? 'flagged' : 'rejected'),
                'risk_score' => $fraudResult['score'],
                'flagged_at' => $fraudResult['status'] === 'flagged' ? now() : null,
                'approved_at' => $fraudResult['status'] === 'approved' ? now() : null,
                'rejected_at' => $fraudResult['status'] === 'rejected' ? now() : null,
            ]);

            foreach ($fraudResult['signals'] as $signal) {
                FraudSignal::create([
                    'entry_id' => $entry->id,
                    'signal_type' => $signal['signal_type'],
                    'score' => $signal['score'],
                    'details' => $signal['details'],
                ]);
            }

            $receipt->update(['status' => $entry->status === 'approved' ? 'accepted' : $entry->status]);

            return $entry;
        });

        if ($entry->status === 'flagged') {
            event(new EntryFlagged($entry));
        }

        if ($entry->status === 'approved') {
            event(new EntryAccepted($entry));
        }

        return $entry;
    }
}
