<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

final readonly class CarDto implements Arrayable
{
    public function __construct(
        private string $name,
        private ?string $description,
        private string $registrationNumber,
        private ?string $technicalDetails
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'registration_number' => $this->getRegistrationNumber(),
            'technical_details' => $this->getTechnicalDetails(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getTechnicalDetails(): ?string
    {
        return $this->technicalDetails;
    }
}
