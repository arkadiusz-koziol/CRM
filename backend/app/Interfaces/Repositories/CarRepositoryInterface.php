<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Dto\CarDto;
use App\Models\Car;

interface CarRepositoryInterface
{
    public function createCar(CarDto $carDto): Car;

    public function updateCar(Car $car, CarDto $carDto): bool;

    public function deleteCar(Car $car): bool;

    public function findAllCars(): array;

    public function findPaginated(int $page = 1, int $limit = 10, string $search = ''): array;

    public function findById(int $id): ?Car;
}
