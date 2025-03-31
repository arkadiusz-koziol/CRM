<?php

namespace App\Services;

use App\Dto\PinDto;
use App\Interfaces\Repositories\PinRepositoryInterface;
use App\Models\Pin;
use App\Models\Plan;

class PinService
{
    public function __construct(
        protected PinRepositoryInterface $pinRepository
    ) {}

    public function createPin(PinDto $dto, Plan $plan): Pin
    {
        return $this->pinRepository->create([
            'user_id' => $dto->userId,
            'plan_id' => $plan->id,
            'x' => $dto->x,
            'y' => $dto->y,
            'photo_path' => $dto->photo?->store('pins/photos', 'public'),
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
