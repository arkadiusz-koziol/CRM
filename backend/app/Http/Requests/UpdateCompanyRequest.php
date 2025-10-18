<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use App\Rules\UniqueIgnoringSoftDeletes;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'industry' => ['sometimes', 'nullable', 'string', 'max:255'],
            'source' => ['sometimes', 'string', 'in:'.implode(',', array_column(CompanySource::cases(), 'value'))],
            'status' => ['sometimes', 'string', 'in:'.implode(',', array_column(CompanyStatus::cases(), 'value'))],
            'region' => ['sometimes', 'nullable', 'string', 'max:255'],
            'vat_id' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
                new UniqueIgnoringSoftDeletes('companies', 'vat_id', $this->route('company')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Company name cannot exceed 255 characters.',
            'source.in' => 'Invalid company source.',
            'status.in' => 'Invalid company status.',
            'vat_id.unique' => 'A company with this VAT ID already exists.',
        ];
    }
}
