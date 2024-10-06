<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'surname' => ['string', 'max:255'],
            'phone' => ['string', 'max:255', 'unique:users,phone,' . $this->route('id')],
            'email' => ['email', 'max:255', 'unique:users,email,' . $this->route('id')],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('validation.unique', ['attribute' => __(('validation.attributes.email'))]),
            'phone.unique' => __('validation.unique', ['attribute' => __(('validation.attributes.phone'))]),
        ];
    }
}
