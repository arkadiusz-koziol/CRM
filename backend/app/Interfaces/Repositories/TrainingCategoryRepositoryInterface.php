<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\TrainingCategory\Entity\TrainingCategory as TrainingCategoryEntity;
use App\Dto\TrainingCategoryDto;
use Illuminate\Database\Eloquent\Collection;

interface TrainingCategoryRepositoryInterface
{
    public function create(TrainingCategoryDto $trainingCategoryDto): TrainingCategoryEntity;

    public function findById(string $id): ?TrainingCategoryEntity;

    public function findAll(): Collection;

    public function update(TrainingCategoryEntity $trainingCategoryEntity, TrainingCategoryDto $trainingCategoryDto): TrainingCategoryEntity;

    public function delete(TrainingCategoryEntity $trainingCategoryEntity): bool;
}
