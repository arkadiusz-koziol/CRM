<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Billing\ContractStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BulkUpdateContractStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string', 'uuid', 'exists:contracts,id'],
            'status' => ['required', 'string', Rule::in(array_column(ContractStatus::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Contract IDs are required',
            'ids.array' => 'Contract IDs must be an array',
            'ids.min' => 'At least one contract ID is required',
            'ids.*.required' => 'Each contract ID is required',
            'ids.*.string' => 'Each contract ID must be a string',
            'ids.*.uuid' => 'Each contract ID must be a valid UUID',
            'ids.*.exists' => 'One or more contracts do not exist',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: '.implode(', ', array_column(ContractStatus::cases(), 'value')),
        ];
    }
}
