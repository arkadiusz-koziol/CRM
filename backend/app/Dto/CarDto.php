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
            'name' => $this->name,
            'description' => $this->description,
            'registration_number' => $this->registrationNumber,
            'technical_details' => $this->technicalDetails
        ];
    }
}