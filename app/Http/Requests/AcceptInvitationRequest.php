<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AcceptInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Invitation token validates authorization
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Rules differ based on whether user is registering or just accepting
        $rules = [
            'remember' => ['sometimes', 'boolean'],
        ];

        // If this is a new user registration (no existing user with email)
        if ($this->needsRegistration()) {
            $rules = array_merge($rules, [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        }

        return $rules;
    }

    /**
     * Check if user needs to register (doesn't exist yet).
     */
    protected function needsRegistration(): bool
    {
        // This is passed from the controller/view state
        return $this->boolean('needs_registration', false);
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'middle_name' => 'middle name',
            'password' => 'password',
        ];
    }
}
