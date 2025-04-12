<?php

namespace App\Services;

use App\DTO\CarDto;
use App\Interfaces\Repositories\CarRepositoryInterface;
use App\Models\Car;

Class CarService
{
    public function __construct(
        protected CarRepositoryInterface $carRepository
    ) {
    }

    public function createCar(CarDto $carDto): Car
    {
        return $this->carRepository->create($carDto);
    }
}
