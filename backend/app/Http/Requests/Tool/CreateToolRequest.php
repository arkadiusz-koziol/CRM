<?php

namespace App\Http\Requests\Tool;

use App\Rules\UniqueIgnoringSoftDeletes;
use Illuminate\Foundation\Http\FormRequest;

class CreateToolRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new UniqueIgnoringSoftDeletes('tools', 'name')],
            'description' => ['nullable', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:0'],
        ];
    }
}
