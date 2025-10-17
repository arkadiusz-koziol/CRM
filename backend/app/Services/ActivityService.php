<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Activity\Entity\Activity;
use App\Factory\ActivityDtoFactory;
use App\Interfaces\Repositories\ActivityRepositoryInterface;
use Illuminate\Support\Collection;

final class ActivityService
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository
    ) {}

    public function logActivity(
        string $action,
        string $userName,
        string $userEmail,
        string $entityType,
        ?string $entityId = null
    ): void {
        $activity = Activity::create(
            action: $action,
            userName: $userName,
            userEmail: $userEmail,
            entityType: $entityType,
            entityId: $entityId
        );

        $this->activityRepository->save($activity);
    }

    public function getRecentActivities(int $limit = 10): Collection
    {
        return $this->activityRepository
            ->getRecent($limit)
            ->map(fn (Activity $activity) => ActivityDtoFactory::fromEntity($activity));
    }

    public function getActivitiesByEntityType(string $entityType, int $limit = 10): Collection
    {
        return $this->activityRepository
            ->getByEntityType($entityType, $limit)
            ->map(fn (Activity $activity) => ActivityDtoFactory::fromEntity($activity));
    }
}
