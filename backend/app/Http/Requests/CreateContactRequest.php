<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contact.create');
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:contacts,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'lead_level' => ['required', Rule::enum(LeadLevel::class)],
            'owner_user_id' => ['nullable', 'string', 'exists:users,id'],
            'source' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(ContactStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'A contact with this email already exists.',
            'lead_level.required' => 'Lead level is required.',
            'lead_level.enum' => 'Lead level must be a valid value.',
            'owner_user_id.exists' => 'The selected owner user does not exist.',
            'source.required' => 'Source is required.',
            'status.required' => 'Status is required.',
            'status.enum' => 'Status must be a valid value.',
        ];
    }
}
