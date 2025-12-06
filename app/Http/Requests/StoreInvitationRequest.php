<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Invitation::class);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'invited_by' => ['required', 'exists:users,id'],
            'organization_id' => ['required', 'exists:organizations,id'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
