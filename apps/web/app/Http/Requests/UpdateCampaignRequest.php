<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->hasRole('admin') || $user->can('campaigns.manage');
    }

    public function rules(): array
    {
        $campaignId = $this->route('campaign')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('campaigns', 'slug')->ignore($campaignId)],
            'status' => ['required', 'in:draft,active,completed,archived'],
            'description' => ['nullable', 'string'],
            'timezone' => ['required', 'string', 'max:64'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'final_draw_at' => ['required', 'date', 'after_or_equal:ends_at'],
            'terms_url' => ['nullable', 'url'],
        ];
    }
}
