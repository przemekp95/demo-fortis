<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->hasRole('admin') || $user->can('campaigns.manage');
    }

    /** @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|\Illuminate\Contracts\Validation\ValidationRule|string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:campaigns,slug'],
            'status' => ['required', 'in:draft,active,completed,archived'],
            'description' => ['nullable', 'string'],
            'timezone' => ['required', 'string', 'max:64'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'final_draw_at' => ['required', 'date', 'after_or_equal:ends_at'],
            'terms_url' => ['nullable', 'url'],
            'rule.max_entries_per_day' => ['required', 'integer', 'min:1', 'max:200'],
            'rule.velocity_per_hour' => ['required', 'integer', 'min:1', 'max:50'],
            'rule.max_receipt_age_days' => ['required', 'integer', 'min:1', 'max:60'],
            'rule.min_purchase_amount' => ['required', 'numeric', 'min:0.01'],
            'rule.deduplicate_receipts' => ['required', 'boolean'],
            'rule.risk_score_flag_threshold' => ['required', 'numeric', 'min:1', 'max:100'],
            'rule.risk_score_reject_threshold' => ['required', 'numeric', 'min:1', 'max:100'],
            'prizes' => ['required', 'array', 'min:1'],
            'prizes.*.name' => ['required', 'string', 'max:255'],
            'prizes.*.draw_type' => ['required', 'in:daily,final'],
            'prizes.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'prizes.*.value' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
