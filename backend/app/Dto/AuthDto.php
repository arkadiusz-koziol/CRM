<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class AuthDto implements Arrayable
{
    public string $email;
    public string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
