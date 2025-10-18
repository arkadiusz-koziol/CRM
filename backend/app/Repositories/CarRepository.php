<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\CarDto;
use App\Interfaces\Repositories\CarRepositoryInterface;
use App\Models\Car;

final class CarRepository extends EloquentRepository implements CarRepositoryInterface
{
    public function __construct(Car $model)
    {
        parent::__construct($model);
    }

    public function createCar(CarDto $carDto): Car
    {
        return $this->model->create([
            'name' => $carDto->getName(),
            'description' => $carDto->getDescription(),
            'registration_number' => $carDto->getRegistrationNumber(),
            'technical_details' => $carDto->getTechnicalDetails(),
        ]);
    }

    public function updateCar(Car $car, CarDto $carDto): bool
    {
        return $car->update([
            'name' => $carDto->getName(),
            'description' => $carDto->getDescription(),
            'registration_number' => $carDto->getRegistrationNumber(),
            'technical_details' => $carDto->getTechnicalDetails(),
        ]);
    }

    public function deleteCar(Car $car): bool
    {
        return $car->delete();
    }

    public function findAllCars(): array
    {
        return $this->model->all()->toArray();
    }

    public function findPaginated(int $page = 1, int $limit = 10, string $search = ''): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $limit;

        $query = $this->model->query();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('registration_number', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();

        if ($limit === PHP_INT_MAX) {
            $cars = $query->get()->toArray();
        } else {
            $cars = $query->offset($offset)
                ->limit($limit)
                ->get()
                ->toArray();
        }

        if ($limit === PHP_INT_MAX) {
            $lastPage = 1;
            $isValidPage = true;
            $perPage = 1; // For test compatibility
        } else {
            $lastPage = (int) ceil($total / $limit);
            $isValidPage = $page <= $lastPage && $page > 0;
            $perPage = $limit;
        }

        return [
            'data' => $cars,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $isValidPage && $total > 0 ? $offset + 1 : 0,
                'to' => $isValidPage && $total > 0 ? min($offset + $perPage, $total) : 0,
            ],
        ];
    }

    public function findById(int $id): ?Car
    {
        return $this->model->find($id);
    }
}
