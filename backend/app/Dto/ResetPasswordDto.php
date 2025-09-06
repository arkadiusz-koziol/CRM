<?php

declare(strict_types=1);

namespace App\Dto;

readonly class ResetPasswordDto
{
    public function __construct(
        private string $email,
        private string $password,
        private string $token
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
