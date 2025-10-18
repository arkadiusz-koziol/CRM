<?php

declare(strict_types=1);

namespace App\Infrastructure\Automation;

use App\Domain\Automation\Entity\WorkflowRule;
use App\Models\WorkflowRule as WorkflowRuleModel;
use Carbon\Carbon;

final class WorkflowRuleMapper
{
    public function toDomain(WorkflowRuleModel $model): WorkflowRule
    {
        return new WorkflowRule(
            id: $model->id,
            workflowId: $model->workflow_id,
            name: $model->name,
            description: $model->description,
            conditions: $model->conditions,
            actions: $model->actions,
            priority: $model->priority,
            isActive: $model->is_active,
            createdAt: Carbon::parse($model->created_at),
            updatedAt: Carbon::parse($model->updated_at),
            deletedAt: $model->deleted_at ? Carbon::parse($model->deleted_at) : null
        );
    }

    public function toModel(WorkflowRule $rule): WorkflowRuleModel
    {
        $model = new WorkflowRuleModel;
        $model->id = $rule->id();
        $model->workflow_id = $rule->workflowId();
        $model->name = $rule->name();
        $model->description = $rule->description();
        $model->conditions = $rule->conditions();
        $model->actions = $rule->actions();
        $model->priority = $rule->priority();
        $model->is_active = $rule->isActive();
        $model->created_at = $rule->createdAt();
        $model->updated_at = $rule->updatedAt();
        $model->deleted_at = $rule->deletedAt();

        return $model;
    }
}
