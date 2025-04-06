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

    // public function updateCar(Car $car, CarDto $carDto): bool
    // {
    //     return $this->carRepository->update($car,$carDto);
    // }

    // public function deleteCar($car): bool
    // {
    //     return $this->carRepository->delete($car);
    // }

    // public function findById($id): ?Car
    // {
    //     return $this->carRepository->findById($id);
    // }

    // public function findAll(): array
    // {
    //     return $this->carRepository->findAll();
    // }
}