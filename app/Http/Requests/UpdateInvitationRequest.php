<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['sometimes', 'string', 'email', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:pending,accepted,rejected,expired'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
