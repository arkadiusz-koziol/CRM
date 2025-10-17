<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CarDto;

final class CarDtoFactory
{
    public static function fromArray(array $data): CarDto
    {
        return new CarDto(
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            registrationNumber: strtoupper($data['registration_number'] ?? ''),
            technicalDetails: $data['technical_details'] ?? null
        );
    }
}
