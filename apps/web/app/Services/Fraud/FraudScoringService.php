<?php

namespace App\Services\Fraud;

use App\Models\CampaignRule;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Support\Carbon;

class FraudScoringService
{
    /**
     * @return array{score: float, status: string, signals: array<int, array{signal_type: string, score: float, details: array<string, mixed>}>}
     */
    public function evaluate(Receipt $receipt, CampaignRule $rule, User $user): array
    {
        $score = 0.0;
        $signals = [];

        $hourlyCount = Entry::query()
            ->where('user_id', $user->id)
            ->where('campaign_id', $receipt->campaign_id)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        if ($hourlyCount >= $rule->velocity_per_hour) {
            $score += 35;
            $signals[] = [
                'signal_type' => 'velocity_limit_hour',
                'score' => 35.0,
                'details' => ['count' => $hourlyCount, 'limit' => $rule->velocity_per_hour],
            ];
        }

        $dailyCount = Entry::query()
            ->where('user_id', $user->id)
            ->where('campaign_id', $receipt->campaign_id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($dailyCount >= $rule->max_entries_per_day) {
            $score += 25;
            $signals[] = [
                'signal_type' => 'daily_limit_reached',
                'score' => 25.0,
                'details' => ['count' => $dailyCount, 'limit' => $rule->max_entries_per_day],
            ];
        }

        if ($receipt->submitted_ip !== null) {
            $sharedIpCount = Receipt::query()
                ->where('campaign_id', $receipt->campaign_id)
                ->where('submitted_ip', $receipt->submitted_ip)
                ->whereDate('created_at', Carbon::today())
                ->count();

            if ($sharedIpCount > 20) {
                $score += 20;
                $signals[] = [
                    'signal_type' => 'shared_ip_spike',
                    'score' => 20.0,
                    'details' => ['count' => $sharedIpCount],
                ];
            }
        }

        if ($receipt->device_fingerprint !== null) {
            $fingerprintCount = Receipt::query()
                ->where('campaign_id', $receipt->campaign_id)
                ->where('device_fingerprint', $receipt->device_fingerprint)
                ->whereDate('created_at', Carbon::today())
                ->count();

            if ($fingerprintCount > 5) {
                $score += 20;
                $signals[] = [
                    'signal_type' => 'device_fingerprint_spike',
                    'score' => 20.0,
                    'details' => ['count' => $fingerprintCount],
                ];
            }
        }

        $status = 'approved';
        if ($score >= (float) $rule->risk_score_reject_threshold) {
            $status = 'rejected';
        } elseif ($score >= (float) $rule->risk_score_flag_threshold) {
            $status = 'flagged';
        }

        return [
            'score' => round($score, 2),
            'status' => $status,
            'signals' => $signals,
        ];
    }
}
