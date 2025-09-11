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
    ) {
    }

    public function createPin(PinDto $dto, Plan $plan): Pin
    {
        return $this->pinRepository->create([
            'user_id' => $dto->getUserId(),
            'plan_id' => $plan->id,
            'x' => $dto->getX(),
            'y' => $dto->getY(),
            'photo_path' => $dto->getPhoto()?->store('pins/photos', 'public'),
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
