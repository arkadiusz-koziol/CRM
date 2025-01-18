<?php

namespace App\Interfaces\Repositories;

use App\Models\Pin;

interface PinRepositoryInterface
{
    public function create(array $data): Pin;

    public function findById(int $id): ?Pin;

    public function delete(Pin $pin): bool;

    public function getPinsByPlanId(int $planId): iterable;
}
