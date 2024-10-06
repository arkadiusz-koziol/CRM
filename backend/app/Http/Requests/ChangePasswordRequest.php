<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed:new_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => __('validation.required', ['attribute' => 'password']),
            'password.string' => __('validation.string', ['attribute' => 'password']),
            'password.min' => __('validation.min.string', ['attribute' => 'password', 'min' => 8]),
            'new_password.required' => __('validation.required', ['attribute' => 'new password']),
            'new_password.string' => __('validation.string', ['attribute' => 'new password']),
            'new_password.min' => __('validation.min.string', ['attribute' => 'new password', 'min' => 8]),
            'new_password.confirmed' => __('validation.confirmed', ['attribute' => 'new password']),
        ];
    }
}
