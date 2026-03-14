<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ReviewFraudRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->hasRole('admin') || $user->can('fraud.review');
    }

    /** @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|\Illuminate\Contracts\Validation\ValidationRule|string>> */
    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected,escalated'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
