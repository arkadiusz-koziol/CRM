<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class CreateCityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:cities,name'],
            'district' => ['required', 'string', 'max:255'],
            'commune' => ['required', 'string', 'max:255'],
            'voivodeship' => ['required', 'string', 'max:255'],
            ];
        }
}
