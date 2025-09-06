<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\ResetPasswordDto;
use App\Http\Requests\ResetPasswordRequest;

final class ResetPasswordDtoFactory
{
    public function fromRequest(ResetPasswordRequest $request): ResetPasswordDto
    {
        return new ResetPasswordDto(
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            token: $request->string('token')->toString()
        );
    }
}
