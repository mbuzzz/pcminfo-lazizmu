<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'payer_name' => ['required', 'string', 'max:100'],
            'payer_email' => ['nullable', 'email', 'max:255'],
            'payer_phone' => ['required', 'string', 'max:20'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'amount' => ['nullable', 'integer', 'min:1000'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'message' => ['nullable', 'string', 'max:500'],
            'payload' => ['sometimes', 'array'],
            'idempotency_key' => ['nullable', 'uuid'],
        ];
    }
}
