<?php

namespace App\Dto;

readonly class CreateUserDto
{
    public function __construct(
        private string $name,
        private string $surname,
        private string $email,
        private ?string $phone,
        private string $password,
        private ?string $city,
        private ?string $vovoidship
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getVovoidship(): ?string
    {
        return $this->vovoidship;
    }
}
