<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\CarDto;
use App\Interfaces\Repositories\CarRepositoryInterface;
use App\Models\Car;

class CarService
{
    public function __construct(
        protected CarRepositoryInterface $carRepository
    ) {}

    public function createCar(CarDto $carDto): Car
    {
        return $this->carRepository->createCar($carDto);
    }

    public function updateCar(Car $car, CarDto $carDto): bool
    {
        return $this->carRepository->updateCar($car, $carDto);
    }

    public function getAllCars(): array
    {
        return $this->carRepository->findAllCars();
    }

    public function getPaginatedCars(int $page = 1, int $limit = 10, string $search = ''): array
    {
        return $this->carRepository->findPaginated($page, $limit, $search);
    }

    public function getCarById(int $id): ?Car
    {
        return $this->carRepository->findById($id);
    }
}
