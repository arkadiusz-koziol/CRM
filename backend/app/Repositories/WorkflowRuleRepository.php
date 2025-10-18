<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Automation\Entity\WorkflowRule;
use App\Infrastructure\Automation\WorkflowRuleMapper;
use App\Interfaces\Repositories\WorkflowRuleRepositoryInterface;
use App\Models\WorkflowRule as WorkflowRuleModel;

final class WorkflowRuleRepository implements WorkflowRuleRepositoryInterface
{
    public function __construct(
        private WorkflowRuleMapper $mapper
    ) {}

    public function findById(string $id): ?WorkflowRule
    {
        $model = WorkflowRuleModel::find($id);

        if (! $model) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findByWorkflowId(string $workflowId): array
    {
        $models = WorkflowRuleModel::where('workflow_id', $workflowId)->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function findActiveRules(): array
    {
        $models = WorkflowRuleModel::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $query = WorkflowRuleModel::query();

        if (isset($filters['workflow_id'])) {
            $query->where('workflow_id', $filters['workflow_id']);
        }

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

    public function save(WorkflowRule $rule): void
    {
        $model = $this->mapper->toModel($rule);
        $model->save();
    }

    public function update(WorkflowRule $rule): void
    {
        $model = WorkflowRuleModel::find($rule->id());

        if (! $model) {
            throw new \RuntimeException('Workflow rule not found');
        }

        $updatedModel = $this->mapper->toModel($rule);
        $updatedModel->save();
    }

    public function delete(string $id): void
    {
        $model = WorkflowRuleModel::find($id);

        if ($model) {
            $model->delete();
        }
    }
}
