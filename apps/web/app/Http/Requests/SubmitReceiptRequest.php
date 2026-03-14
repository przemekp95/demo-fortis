<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|\Illuminate\Contracts\Validation\ValidationRule|string>> */
    public function rules(): array
    {
        return [
            'receipt_number' => ['required', 'string', 'max:64'],
            'purchase_amount' => ['required', 'numeric', 'min:0.01'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'device_fingerprint' => ['nullable', 'string', 'max:128'],
        ];
    }
}
