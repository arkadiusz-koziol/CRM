<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignUsersByRoleToTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => __('app.validation.role_required'),
            'role.string' => __('app.validation.role_string'),
            'role.max' => __('app.validation.role_max'),
        ];
    }
}
