<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
{
    public function rules(): array
    {
        $city = $this->route('city');

        return [
            'name' => ['required', 'string', 'max:255', 'unique:cities,name,'.$city->id],
            'district' => ['required', 'string', 'max:255'],
            'commune' => ['required', 'string', 'max:255'],
            'voivodeship' => ['required', 'string', 'max:255'],
        ];
    }
}
