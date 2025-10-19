<?php

declare(strict_types=1);

namespace App\Http\Requests\Export;

use Illuminate\Foundation\Http\FormRequest;

final class ExportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'data' => 'array',
            'data.*' => 'array',
            'filename' => 'nullable|string|max:255',
            'headers' => 'nullable|array',
            'headers.*' => 'string|max:255',
            'options' => 'nullable|array',
            'options.title' => 'nullable|string|max:255',
            'options.description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'data.array' => 'Export data must be an array.',
            'data.*.array' => 'Each data row must be an array.',
            'filename.string' => 'Filename must be a string.',
            'filename.max' => 'Filename cannot exceed 255 characters.',
            'headers.array' => 'Headers must be an array.',
            'headers.*.string' => 'Each header must be a string.',
            'headers.*.max' => 'Each header cannot exceed 255 characters.',
            'options.array' => 'Options must be an array.',
            'options.title.string' => 'Title must be a string.',
            'options.title.max' => 'Title cannot exceed 255 characters.',
            'options.description.string' => 'Description must be a string.',
            'options.description.max' => 'Description cannot exceed 1000 characters.',
        ];
    }
}
