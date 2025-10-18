<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Billing\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:255', 'unique:invoices,nr'],
            'company_id' => ['required', 'string', 'exists:companies,id'],
            'contract_id' => ['nullable', 'string', 'exists:contracts,id'],
            'issue_date' => ['required', 'date', 'before_or_equal:due_date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['required', 'string', Rule::in(array_column(InvoiceStatus::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Invoice number is required',
            'number.unique' => 'Invoice number must be unique',
            'company_id.required' => 'Company ID is required',
            'company_id.exists' => 'Company does not exist',
            'contract_id.exists' => 'Contract does not exist',
            'issue_date.required' => 'Issue date is required',
            'issue_date.before_or_equal' => 'Issue date must be before or equal to due date',
            'due_date.required' => 'Due date is required',
            'due_date.after_or_equal' => 'Due date must be after or equal to issue date',
            'amount.required' => 'Amount is required',
            'amount.min' => 'Amount must be greater than or equal to 0',
            'currency.required' => 'Currency is required',
            'currency.size' => 'Currency must be exactly 3 characters',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: '.implode(', ', array_column(InvoiceStatus::cases(), 'value')),
        ];
    }
}
