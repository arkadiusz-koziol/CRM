<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Billing\ContractStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateContractRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:255', 'unique:contracts,nr'],
            'company_id' => ['required', 'string', 'exists:companies,id'],
            'start_at' => ['required', 'date', 'before_or_equal:end_at'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['required', 'string', Rule::in(array_column(ContractStatus::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Contract number is required',
            'number.unique' => 'Contract number must be unique',
            'company_id.required' => 'Company ID is required',
            'company_id.exists' => 'Company does not exist',
            'start_at.required' => 'Start date is required',
            'start_at.before_or_equal' => 'Start date must be before or equal to end date',
            'end_at.required' => 'End date is required',
            'end_at.after_or_equal' => 'End date must be after or equal to start date',
            'amount.required' => 'Amount is required',
            'amount.min' => 'Amount must be greater than or equal to 0',
            'currency.required' => 'Currency is required',
            'currency.size' => 'Currency must be exactly 3 characters',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: '.implode(', ', array_column(ContractStatus::cases(), 'value')),
        ];
    }
}
