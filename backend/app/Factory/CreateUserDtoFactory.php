<?php

namespace App\Factory;

use App\Dto\CreateUserDto;
use App\Http\Requests\CreateUserRequest;

class CreateUserDtoFactory
{
    public function fromRequest(CreateUserRequest $request): CreateUserDto
    {
        return new CreateUserDto(
            name: $request->string('name')->toString(),
            surname: $request->string('surname')->toString(),
            email: $request->string('email')->toString(),
            phone: $request->filled('phone') ? $request->string('phone')->toString() : null,
            password: $request->string('password')->toString(),
            city: $request->filled('city') ? $request->string('city')->toString() : null,
            vovoidship: $request->filled('vovoidship') ? $request->string('vovoidship')->toString() : null
        );
    }
}
