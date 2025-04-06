<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'registration_number' => ['required', 'alpha_num','max:255','unique:cars,registration_number'],
            'technical_details' => ['required','string','max:255']
        ];
    }
}