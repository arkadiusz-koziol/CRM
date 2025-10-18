<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use App\Rules\UniqueIgnoringSoftDeletes;
use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'source' => ['required', 'string', 'in:'.implode(',', array_column(CompanySource::cases(), 'value'))],
            'status' => ['nullable', 'string', 'in:'.implode(',', array_column(CompanyStatus::cases(), 'value'))],
            'region' => ['nullable', 'string', 'max:255'],
            'vat_id' => [
                'nullable',
                'string',
                'max:50',
                new UniqueIgnoringSoftDeletes('companies', 'vat_id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required.',
            'name.max' => 'Company name cannot exceed 255 characters.',
            'source.required' => 'Company source is required.',
            'source.in' => 'Invalid company source.',
            'status.in' => 'Invalid company status.',
            'vat_id.unique' => 'A company with this VAT ID already exists.',
        ];
    }
}
