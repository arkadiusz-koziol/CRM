<?php

namespace App\Http\Requests\Material;

use App\Rules\UniqueIgnoringSoftDeletes;
use Illuminate\Foundation\Http\FormRequest;

class CreateMaterialRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes('materials', 'name')
            ],
            'description' => ['required', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric'],
        ];
    }
}
