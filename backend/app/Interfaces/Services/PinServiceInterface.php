<?php

namespace App\Interfaces\Services;

use App\Models\Pin;
use App\Models\Plan;

interface PinServiceInterface
{
    public function createPin(array $data, Plan $plan): Pin;

    public function deletePin(Pin $pin): bool;

    public function getPinsByPlan(Plan $plan): iterable;
}
