<?php

namespace App\Factory;

use App\Dto\CarDto;

class CarDtoFactory
{
    public static function fromRequest(
        string $name = null,
        string $description = null,
        string $registrationNumber = null,
        ?string $technicalDetails = null
    ): CarDto {
        return new CarDto(
            name: $name,
            description: $description,
            registrationNumber: strtoupper($registrationNumber),
            technicalDetails: $technicalDetails ?? null
        );
    }
}
