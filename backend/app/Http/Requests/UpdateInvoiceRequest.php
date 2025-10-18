<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Billing\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        $invoiceId = $this->route('invoice');

        return [
            'number' => ['sometimes', 'string', 'max:255', Rule::unique('invoices', 'nr')->ignore($invoiceId)],
            'company_id' => ['sometimes', 'string', 'exists:companies,id'],
            'contract_id' => ['nullable', 'string', 'exists:contracts,id'],
            'issue_date' => ['sometimes', 'date', 'before_or_equal:due_date'],
            'due_date' => ['sometimes', 'date', 'after_or_equal:issue_date'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'status' => ['sometimes', 'string', Rule::in(array_column(InvoiceStatus::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'number.unique' => 'Invoice number must be unique',
            'company_id.exists' => 'Company does not exist',
            'contract_id.exists' => 'Contract does not exist',
            'issue_date.before_or_equal' => 'Issue date must be before or equal to due date',
            'due_date.after_or_equal' => 'Due date must be after or equal to issue date',
            'amount.min' => 'Amount must be greater than or equal to 0',
            'currency.size' => 'Currency must be exactly 3 characters',
            'status.in' => 'Status must be one of: '.implode(', ', array_column(InvoiceStatus::cases(), 'value')),
        ];
    }
}
