<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Billing\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BulkUpdateInvoiceStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string', 'uuid', 'exists:invoices,id'],
            'status' => ['required', 'string', Rule::in(array_column(InvoiceStatus::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Invoice IDs are required',
            'ids.array' => 'Invoice IDs must be an array',
            'ids.min' => 'At least one invoice ID is required',
            'ids.*.required' => 'Each invoice ID is required',
            'ids.*.string' => 'Each invoice ID must be a string',
            'ids.*.uuid' => 'Each invoice ID must be a valid UUID',
            'ids.*.exists' => 'One or more invoices do not exist',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: '.implode(', ', array_column(InvoiceStatus::cases(), 'value')),
        ];
    }
}
