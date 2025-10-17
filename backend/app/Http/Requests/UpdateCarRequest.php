<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateCarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'registration_number' => ['required', 'string', 'max:20'],
            'technical_details' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('app.validation.name_required'),
            'name.string' => __('app.validation.name_string'),
            'name.max' => __('app.validation.name_max'),
            'description.string' => __('app.validation.description_string'),
            'description.max' => __('app.validation.description_max'),
            'registration_number.required' => __('app.validation.registration_number_required'),
            'registration_number.string' => __('app.validation.registration_number_string'),
            'registration_number.max' => __('app.validation.registration_number_max'),
            'technical_details.string' => __('app.validation.technical_details_string'),
            'technical_details.max' => __('app.validation.technical_details_max'),
        ];
    }
}

