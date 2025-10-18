<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\TrainingCategory\Entity\TrainingCategory as TrainingCategoryEntity;
use App\Dto\TrainingCategoryDto;
use App\Infrastructure\TrainingCategory\Mapper\TrainingCategoryMapper;
use App\Interfaces\Repositories\TrainingCategoryRepositoryInterface;
use App\Models\TrainingCategory;
use Illuminate\Database\Eloquent\Collection;

final class TrainingCategoryRepository extends EloquentRepository implements TrainingCategoryRepositoryInterface
{
    public function __construct(TrainingCategory $model)
    {
        parent::__construct($model);
    }

    public function createTrainingCategory(TrainingCategoryDto $trainingCategoryDto): TrainingCategoryEntity
    {
        $model = $this->model->create($trainingCategoryDto->toArray());

        return TrainingCategoryMapper::toEntity($model);
    }

    public function findTrainingCategoryById(string $id): ?TrainingCategoryEntity
    {
        $model = $this->model->find($id);

        return $model ? TrainingCategoryMapper::toEntity($model) : null;
    }

    public function findAll(): Collection
    {
        $models = $this->model->all();
        $entities = $models->map(function (TrainingCategory $model) {
            return TrainingCategoryMapper::toEntity($model);
        });

        return new \Illuminate\Database\Eloquent\Collection($entities->toArray());
    }

    public function updateTrainingCategory(TrainingCategoryEntity $trainingCategoryEntity, TrainingCategoryDto $trainingCategoryDto): TrainingCategoryEntity
    {
        $model = $this->model->find($trainingCategoryEntity->id());
        if (! $model) {
            throw new \RuntimeException('Training category not found');
        }

        $model->update($trainingCategoryDto->toArray());

        return TrainingCategoryMapper::toEntity($model);
    }

    public function deleteTrainingCategory(TrainingCategoryEntity $trainingCategoryEntity): bool
    {
        $model = $this->model->find($trainingCategoryEntity->id());

        return $model ? $model->delete() : false;
    }
}
