<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateTrainingRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'file' => ['nullable', 'file', 'mimes:pptx,pdf', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.training.title.required'),
            'title.string' => __('validation.training.title.string'),
            'title.max' => __('validation.training.title.max'),
            'description.string' => __('validation.training.description.string'),
            'category.required' => __('validation.training.category.required'),
            'category.string' => __('validation.training.category.string'),
            'category.max' => __('validation.training.category.max'),
            'file.file' => __('validation.training.file.file'),
            'file.mimes' => __('validation.training.file.mimes'),
            'file.max' => __('validation.training.file.max'),
        ];
    }
}
