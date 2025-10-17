<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignSelectedUsersToTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.required' => __('app.validation.user_ids_required'),
            'user_ids.array' => __('app.validation.user_ids_array'),
            'user_ids.min' => __('app.validation.user_ids_min'),
            'user_ids.*.integer' => __('app.validation.user_ids_integer'),
            'user_ids.*.exists' => __('app.validation.user_ids_exists'),
        ];
    }
}
