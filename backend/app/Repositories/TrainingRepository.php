<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\TrainingDto;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Models\Training;

final class TrainingRepository extends EloquentRepository implements TrainingRepositoryInterface
{
    public function __construct(Training $model)
    {
        parent::__construct($model);
    }

    public function createTraining(TrainingDto $trainingDto): Training
    {
        return $this->create($trainingDto->toArray());
    }

    public function updateTraining(Training $training, TrainingDto $trainingDto): bool
    {
        try {
            return $this->update($training, $trainingDto->toArray());
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findById(int $id): ?Training
    {
        return $this->model->find($id);
    }

    public function findAllTrainings(): array
    {
        return $this->model->all()->toArray();
    }

    public function findPaginated(int $page = 1, int $limit = 10, string $search = ''): array
    {
        $query = $this->model->query();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $searchTerm = strtolower($search);
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(category) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        $paginated = $query->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ],
        ];
    }
}
