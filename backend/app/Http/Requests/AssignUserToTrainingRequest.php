<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignUserToTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('app.validation.user_id_required'),
            'user_id.integer' => __('app.validation.user_id_integer'),
            'user_id.exists' => __('app.validation.user_id_exists'),
        ];
    }
}
