<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class CreateContactDto
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email,
        private ?string $phone,
        private string $leadLevel,
        private ?string $ownerUserId,
        private string $source,
        private string $status
    ) {}

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function leadLevel(): string
    {
        return $this->leadLevel;
    }

    public function ownerUserId(): ?string
    {
        return $this->ownerUserId;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function status(): string
    {
        return $this->status;
    }
}
