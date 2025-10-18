<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\TrainingCategory\Entity\TrainingCategory as TrainingCategoryEntity;
use App\Factory\TrainingCategoryDtoFactory;
use App\Interfaces\Repositories\TrainingCategoryRepositoryInterface;
use Illuminate\Support\Collection;

final class TrainingCategoryService
{
    public function __construct(
        private TrainingCategoryRepositoryInterface $trainingCategoryRepository,
    ) {}

    public function createCategory(array $data): TrainingCategoryEntity
    {
        $trainingCategoryDto = TrainingCategoryDtoFactory::fromArray($data);

        return $this->trainingCategoryRepository->createTrainingCategory($trainingCategoryDto);
    }

    public function getCategory(string $id): ?TrainingCategoryEntity
    {
        return $this->trainingCategoryRepository->findTrainingCategoryById($id);
    }

    public function getAllCategories(): Collection
    {
        return $this->trainingCategoryRepository->findAll();
    }

    public function updateCategory(string $id, array $data): TrainingCategoryEntity
    {
        $trainingCategory = $this->trainingCategoryRepository->findTrainingCategoryById($id);
        if (! $trainingCategory) {
            throw new \RuntimeException('Training category not found');
        }

        $trainingCategoryDto = TrainingCategoryDtoFactory::fromArray($data);

        return $this->trainingCategoryRepository->updateTrainingCategory($trainingCategory, $trainingCategoryDto);
    }

    public function deleteCategory(string $id): bool
    {
        $trainingCategory = $this->trainingCategoryRepository->findTrainingCategoryById($id);
        if (! $trainingCategory) {
            throw new \RuntimeException('Training category not found');
        }

        return $this->trainingCategoryRepository->deleteTrainingCategory($trainingCategory);
    }
}
