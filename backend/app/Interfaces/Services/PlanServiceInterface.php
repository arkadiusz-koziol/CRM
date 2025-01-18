<?php

namespace App\Interfaces\Services;

use App\Models\Estate;
use App\Models\Plan;

interface PlanServiceInterface
{
    public function createPlan(array $data, Estate $estate): Plan;

    public function deletePlan(Plan $plan): bool;

    public function getPlansByEstate(Estate $estate): Plan;
}
