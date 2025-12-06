<?php

namespace App\Http\Requests;

use App\DataTransferObjects\OwnerData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],

            // Owner details
            'owner_first_name' => ['required', 'string', 'max:255'],
            'owner_last_name' => ['nullable', 'string', 'max:255'],
            'owner_middle_name' => ['nullable', 'string', 'max:255'],
            'owner_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'owner_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'organization name',
            'owner_first_name' => 'first name',
            'owner_last_name' => 'last name',
            'owner_middle_name' => 'middle name',
            'owner_email' => 'email',
            'owner_phone' => 'phone number',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide an organization name.',
            'owner_first_name.required' => 'Please provide the owner\'s first name.',
            'owner_email.required' => 'Please provide the owner\'s email address.',
            'owner_email.email' => 'Please provide a valid email address.',
            'owner_email.unique' => 'This email address is already registered.',
        ];
    }

    /**
     * Get owner data as DTO.
     */
    public function getOwnerData(): OwnerData
    {
        return new OwnerData(
            email: $this->input('owner_email'),
            firstName: $this->input('owner_first_name'),
            lastName: $this->input('owner_last_name'),
            middleName: $this->input('owner_middle_name'),
            phone: $this->input('owner_phone'),
        );
    }
}
