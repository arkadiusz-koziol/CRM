<?php

namespace App\Repositories;

use App\Interfaces\Repositories\PinRepositoryInterface;
use App\Models\Pin;

class PinRepository implements PinRepositoryInterface
{
    public function create(array $data): Pin
    {
        return Pin::create($data);
    }

    public function findById(int $id): ?Pin
    {
        return Pin::find($id);
    }

    public function delete(Pin $pin): bool
    {
        return $pin->delete();
    }

    public function getPinsByPlanId(int $planId): iterable
    {
        return Pin::where('plan_id', $planId)->get();
    }
}
