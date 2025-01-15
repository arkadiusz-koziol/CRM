<?php

namespace App\Interfaces\Repositories;

use App\Models\Plan;

interface PlanRepositoryInterface
{
    public function create(array $data): Plan;

    public function findById(int $id): ?Plan;

    public function delete(Plan $plan): bool;

    public function getPlansByEstateId(int $estateId): iterable;
}
