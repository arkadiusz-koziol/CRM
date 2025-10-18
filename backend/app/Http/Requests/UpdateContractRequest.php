<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Billing\ContractStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateContractRequest extends FormRequest
{
    public function rules(): array
    {
        $contractId = $this->route('contract');

        return [
            'number' => ['sometimes', 'string', 'max:255', Rule::unique('contracts', 'nr')->ignore($contractId)],
            'company_id' => ['sometimes', 'string', 'exists:companies,id'],
            'start_at' => ['sometimes', 'date', 'before_or_equal:end_at'],
            'end_at' => ['sometimes', 'date', 'after_or_equal:start_at'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'status' => ['sometimes', 'string', Rule::in(array_column(ContractStatus::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'number.unique' => 'Contract number must be unique',
            'company_id.exists' => 'Company does not exist',
            'start_at.before_or_equal' => 'Start date must be before or equal to end date',
            'end_at.after_or_equal' => 'End date must be after or equal to start date',
            'amount.min' => 'Amount must be greater than or equal to 0',
            'currency.size' => 'Currency must be exactly 3 characters',
            'status.in' => 'Status must be one of: '.implode(', ', array_column(ContractStatus::cases(), 'value')),
        ];
    }
}
