<?php

declare(strict_types=1);

namespace App\Infrastructure\TrainingCategory\Mapper;

use App\Domain\TrainingCategory\Entity\TrainingCategory as TrainingCategoryEntity;
use App\Models\TrainingCategory;

final class TrainingCategoryMapper
{
    public static function toEntity(TrainingCategory $model): TrainingCategoryEntity
    {
        return new TrainingCategoryEntity(
            id: $model->id,
            name: $model->name,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }

    public static function toModel(TrainingCategoryEntity $entity): TrainingCategory
    {
        $model = new TrainingCategory;
        $model->id = $entity->id();
        $model->name = $entity->name();
        $model->created_at = $entity->createdAt();
        $model->updated_at = $entity->updatedAt();
        $model->deleted_at = $entity->deletedAt();

        return $model;
    }
}
