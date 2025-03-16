<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateToolRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:tools,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:0'],
        ];
    }

}
