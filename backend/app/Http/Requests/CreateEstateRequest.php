<?php

namespace App\Http\Requests;

use App\Rules\UniqueIgnoringSoftDeletes;
use Illuminate\Foundation\Http\FormRequest;

class CreateEstateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'custom_id' => ['nullable', 'string', new UniqueIgnoringSoftDeletes('estates', 'custom_id')],
            'street' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['required', 'integer', 'max:255'],
            'house_number' => ['required', 'string', 'max:20'],
        ];
    }
}
