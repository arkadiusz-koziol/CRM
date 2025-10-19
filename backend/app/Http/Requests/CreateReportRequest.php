<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Reports\ReportSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'source' => ['required', 'string', Rule::enum(ReportSource::class)],
            'columns' => ['required', 'array', 'min:1'],
            'columns.*' => ['required', 'string'],
            'filters' => ['nullable', 'array'],
            'filters.*.field' => ['required_with:filters', 'string'],
            'filters.*.operator' => ['required_with:filters', 'string'],
            'filters.*.value' => ['required_with:filters'],
            'sorting' => ['nullable', 'array'],
            'sorting.*.field' => ['required_with:sorting', 'string'],
            'sorting.*.direction' => ['required_with:sorting', 'string', 'in:asc,desc'],
            'is_public' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Report name is required.',
            'name.max' => 'Report name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'source.required' => 'Report source is required.',
            'source.enum' => 'Invalid report source.',
            'columns.required' => 'At least one column must be selected.',
            'columns.min' => 'At least one column must be selected.',
            'filters.*.field.required_with' => 'Filter field is required.',
            'filters.*.operator.required_with' => 'Filter operator is required.',
            'filters.*.value.required_with' => 'Filter value is required.',
            'sorting.*.field.required_with' => 'Sort field is required.',
            'sorting.*.direction.required_with' => 'Sort direction is required.',
            'sorting.*.direction.in' => 'Sort direction must be asc or desc.',
        ];
    }
}
