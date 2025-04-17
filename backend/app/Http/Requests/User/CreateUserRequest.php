<?php

namespace App\Http\Requests\User;

use App\Rules\UniqueIgnoringSoftDeletes;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', new UniqueIgnoringSoftDeletes('users', 'email')],
            'phone' => ['nullable', 'string', 'max:11', new UniqueIgnoringSoftDeletes('users', 'phone')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'city' => ['nullable', 'string', 'max:255'],
            'vovoidship' => ['nullable', 'string', 'max:255'],
        ];
    }
}
