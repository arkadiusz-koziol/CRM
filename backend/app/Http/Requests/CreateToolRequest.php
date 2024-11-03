<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateToolRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

}
