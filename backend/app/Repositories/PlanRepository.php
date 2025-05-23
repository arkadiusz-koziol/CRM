<?php

namespace App\Repositories;

use App\Interfaces\Repositories\PlanRepositoryInterface;
use App\Models\Plan;
use Illuminate\Support\Collection;

class PlanRepository implements PlanRepositoryInterface
{
    public function create(array $data): Plan
    {
        return Plan::create($data);
    }

    public function findById(int $id): ?Plan
    {
        return Plan::find($id);
    }

    public function delete(Plan $plan): bool
    {
        return $plan->delete();
    }

    public function getPlansByEstateId(int $estateId): Collection
    {
        return Plan::where('estate_id', $estateId)->get();
    }
}
