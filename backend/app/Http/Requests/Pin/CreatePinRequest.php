<?php

namespace App\Http\Requests\Pin;

use Illuminate\Foundation\Http\FormRequest;

class CreatePinRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'x' => ['required', 'numeric', 'between:0,100'],
            'y' => ['required', 'numeric', 'between:0,100'],
            'photo' => ['required', 'file', 'mimes:jpeg,png'],
        ];
    }
}
