<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Dto\TrainingDto;
use App\Models\Training;

interface TrainingRepositoryInterface
{
    public function createTraining(TrainingDto $trainingDto): Training;

    public function findById(int $id): ?Training;

    public function findAllTrainings(): array;

    public function findPaginated(int $page = 1, int $limit = 10, string $search = ''): array;
}
