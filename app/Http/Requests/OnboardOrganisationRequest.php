<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class OnboardOrganisationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'organisation_name' => ['required', 'string', 'max:255'],
            'organisation_slug' => ['required', 'string', 'max:255', 'unique:organizations,slug', 'regex:/^[a-z0-9-]+$/'],
            'organisation_email' => ['nullable', 'email', 'max:255'],
            'owner_first_name' => ['required', 'string', 'max:255'],
            'owner_last_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'owner_phone' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'organisation_name' => 'organisation name',
            'organisation_slug' => 'organisation domain',
            'organisation_email' => 'organisation email',
            'owner_first_name' => 'first name',
            'owner_last_name' => 'last name',
            'owner_email' => 'email address',
            'owner_phone' => 'phone number',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'organisation_slug.unique' => 'This domain is already taken. Please choose a different one.',
            'organisation_slug.regex' => 'The domain can only contain lowercase letters, numbers, and hyphens.',
            'owner_email.unique' => 'An account with this email already exists.',
        ];
    }
}
