<?php

namespace App\Dto;

use Illuminate\Contracts\Support\Arrayable;

class CarDto implements Arrayable
{
    public function __construct(
        public string $name,
        public string $description,
        public string $registrationNumber,
        public string $technicalDetails
    )
    {}

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'registration_number' => $this->getRegistrationNumber(),
            'technical_details' => $this->getTechnicalDetails()
        ];
    }
    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getRegistrationNumber(): string {
        return $this->registrationNumber;
    }

    public function getTechnicalDetails(): string {
        return $this->technicalDetails;
    }
}
