<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\ForgotPasswordDto;
use App\Http\Requests\ForgotPasswordRequest;

final class ForgotPasswordDtoFactory
{
    public function fromRequest(ForgotPasswordRequest $request): ForgotPasswordDto
    {
        return new ForgotPasswordDto(
            email: $request->string('email')->toString()
        );
    }
}
