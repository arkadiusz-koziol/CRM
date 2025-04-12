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
            'name' => $carDto->name,
            'description' => $carDto->description,
            'registration_number' => $carDto->registrationNumber,
            'technical_details' => $carDto->technicalDetails,
        ]);
    }

}
