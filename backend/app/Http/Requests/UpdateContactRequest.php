<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contact.update');
    }

    public function rules(): array
    {
        $contactId = $this->route('contact');

        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('contacts', 'email')->ignore($contactId)],
            'phone' => ['nullable', 'string', 'max:50'],
            'lead_level' => ['sometimes', Rule::enum(LeadLevel::class)],
            'owner_user_id' => ['nullable', 'string', 'exists:users,id'],
            'source' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', Rule::enum(ContactStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.string' => 'First name must be a string.',
            'last_name.string' => 'Last name must be a string.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'A contact with this email already exists.',
            'lead_level.enum' => 'Lead level must be a valid value.',
            'owner_user_id.exists' => 'The selected owner user does not exist.',
            'source.string' => 'Source must be a string.',
            'status.enum' => 'Status must be a valid value.',
        ];
    }
}
