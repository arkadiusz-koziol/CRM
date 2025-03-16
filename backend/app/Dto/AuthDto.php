<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class AuthDto implements Arrayable
{

    public function __construct(
        public string $email,
        public string $password
    ) {
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
