<?php

namespace App\Interfaces\Repositories;

use App\Dto\CarDto;
use App\Models\Car;

interface CarRepositoryInterface
{
    public function create(CarDto $carDto): Car;

    // public function update(Car $car, CarDto $carDto): bool;

    // public function delete(Car $car): bool;

    // public function findById(int $id): ?Car;

    // public function findAll(): array;

}