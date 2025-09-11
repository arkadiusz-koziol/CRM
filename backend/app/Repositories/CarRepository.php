<?php

namespace App\Repositories;

use App\Dto\CarDto;
use App\Interfaces\Repositories\CarRepositoryInterface;
use App\Models\Car;

class CarRepository implements CarRepositoryInterface
{
    public function create(CarDto $carDto): Car
    {
        return Car::create([
            'name' => $carDto->getName(),
            'description' => $carDto->getDescription(),
            'registration_number' => $carDto->getRegistrationNumber(),
            'technical_details' => $carDto->getTechnicalDetails(),
        ]);
    }
}
