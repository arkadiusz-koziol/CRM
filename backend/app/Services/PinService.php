<?php

namespace App\Services;

use App\Interfaces\Repositories\PinRepositoryInterface;
use App\Models\Pin;
use App\Models\Plan;

class PinService
{
    public function __construct(
        protected PinRepositoryInterface $pinRepository
    ) {}

    public function createPin(array $data, Plan $plan): Pin
    {
        $photoPath = null;

        if (isset($data['photo'])) {
            $photoPath = $data['photo']->store('pins/photos', 'public');
        }

        return $this->pinRepository->create([
            'user_id' => $data['user_id'],
            'plan_id' => $plan->id,
            'x' => $data['x'],
            'y' => $data['y'],
            'photo_path' => $photoPath,
        ]);
    }

    public function deletePin(Pin $pin): bool
    {
        return $this->pinRepository->delete($pin);
    }

    public function getPinsByPlan(Plan $plan): iterable
    {
        return $this->pinRepository->getPinsByPlanId($plan->id);
    }
}
