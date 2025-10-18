<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Automation\Entity\Workflow;
use App\Infrastructure\Automation\WorkflowMapper;
use App\Interfaces\Repositories\WorkflowRepositoryInterface;
use App\Models\Workflow as WorkflowModel;

final class WorkflowRepository implements WorkflowRepositoryInterface
{
    public function __construct(
        private WorkflowMapper $mapper
    ) {}

    public function findById(string $id): ?Workflow
    {
        $model = WorkflowModel::find($id);

        if (! $model) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $query = WorkflowModel::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('description', 'like', '%'.$filters['search'].'%');
            });
        }

        $models = $query->limit($limit)->offset($offset)->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function save(Workflow $workflow): void
    {
        $model = $this->mapper->toModel($workflow);
        $model->save();
    }

    public function update(Workflow $workflow): void
    {
        $model = WorkflowModel::find($workflow->id());

        if (! $model) {
            throw new \RuntimeException('Workflow not found');
        }

        $updatedModel = $this->mapper->toModel($workflow);
        $updatedModel->save();
    }

    public function delete(string $id): void
    {
        $model = WorkflowModel::find($id);

        if ($model) {
            $model->delete();
        }
    }
}
