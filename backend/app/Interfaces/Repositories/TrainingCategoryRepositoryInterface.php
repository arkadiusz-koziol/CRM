<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\TrainingCategory\Entity\TrainingCategory as TrainingCategoryEntity;
use App\Dto\TrainingCategoryDto;
use Illuminate\Database\Eloquent\Collection;

interface TrainingCategoryRepositoryInterface
{
    public function createTrainingCategory(TrainingCategoryDto $trainingCategoryDto): TrainingCategoryEntity;

    public function findTrainingCategoryById(string $id): ?TrainingCategoryEntity;

    public function findAll(): Collection;

    public function updateTrainingCategory(TrainingCategoryEntity $trainingCategoryEntity, TrainingCategoryDto $trainingCategoryDto): TrainingCategoryEntity;

    public function deleteTrainingCategory(TrainingCategoryEntity $trainingCategoryEntity): bool;
}
