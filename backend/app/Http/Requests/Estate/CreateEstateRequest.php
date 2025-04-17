<?php

namespace App\Http\Requests\Estate;

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
            'city' => ['required', 'integer', 'exists:cities,id'],
            'house_number' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Pole nazwa jest wymagane.',
            'name.string' => 'Pole nazwa musi być ciągiem znaków.',
            'name.max' => 'Pole nazwa nie może mieć więcej niż 255 znaków.',

            'custom_id.string' => 'Pole identyfikator własny musi być ciągiem znaków.',
            'custom_id.unique' => 'Podany identyfikator własny jest już zajęty.',

            'street.required' => 'Pole ulica jest wymagane.',
            'street.string' => 'Pole ulica musi być ciągiem znaków.',
            'street.max' => 'Pole ulica nie może mieć więcej niż 255 znaków.',

            'postal_code.required' => 'Pole kod pocztowy jest wymagane.',
            'postal_code.string' => 'Pole kod pocztowy musi być ciągiem znaków.',
            'postal_code.max' => 'Pole kod pocztowy nie może mieć więcej niż 20 znaków.',

            'city.required' => 'Pole miasto jest wymagane.',
            'city.integer' => 'Pole miasto musi być liczbą całkowitą.',
            'city.exists' => 'Wybrane miasto jest nieprawidłowe.',

            'house_number.required' => 'Pole numer domu jest wymagane.',
            'house_number.string' => 'Pole numer domu musi być ciągiem znaków.',
            'house_number.max' => 'Pole numer domu nie może mieć więcej niż 20 znaków.',
        ];
    }
}
