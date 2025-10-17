<?php

declare(strict_types=1);

namespace App\Dto;

readonly class ForgotPasswordDto
{
    public function __construct(
        private string $email
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }
}
