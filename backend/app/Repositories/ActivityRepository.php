<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Activity\Entity\Activity;
use App\Interfaces\Repositories\ActivityRepositoryInterface;
use App\Models\Activity as ActivityModel;
use Illuminate\Support\Collection;

final class ActivityRepository implements ActivityRepositoryInterface
{
    public function save(Activity $activity): void
    {
        ActivityModel::create([
            'id' => $activity->id(),
            'action' => $activity->action(),
            'user_name' => $activity->userName(),
            'user_email' => $activity->userEmail(),
            'entity_type' => $activity->entityType(),
            'entity_id' => $activity->entityId(),
            'created_at' => $activity->createdAt(),
        ]);
    }

    public function getRecent(int $limit = 10): Collection
    {
        return ActivityModel::query()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function (ActivityModel $model) {
                return Activity::create(
                    action: $model->action,
                    userName: $model->user_name,
                    userEmail: $model->user_email,
                    entityType: $model->entity_type,
                    entityId: $model->entity_id
                );
            });
    }

    public function getByEntityType(string $entityType, int $limit = 10): Collection
    {
        return ActivityModel::query()
            ->where('entity_type', $entityType)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function (ActivityModel $model) {
                return Activity::create(
                    action: $model->action,
                    userName: $model->user_name,
                    userEmail: $model->user_email,
                    entityType: $model->entity_type,
                    entityId: $model->entity_id
                );
            });
    }
}
