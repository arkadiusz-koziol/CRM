<?php

declare(strict_types=1);

namespace App\Infrastructure\Automation;

use App\Domain\Automation\Entity\Workflow;
use App\Models\Workflow as WorkflowModel;
use Carbon\Carbon;

final class WorkflowMapper
{
    public function toDomain(WorkflowModel $model): Workflow
    {
        return new Workflow(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            isActive: $model->is_active,
            createdAt: Carbon::parse($model->created_at),
            updatedAt: Carbon::parse($model->updated_at),
            deletedAt: $model->deleted_at ? Carbon::parse($model->deleted_at) : null
        );
    }

    public function toModel(Workflow $workflow): WorkflowModel
    {
        $model = new WorkflowModel;
        $model->id = $workflow->id();
        $model->name = $workflow->name();
        $model->description = $workflow->description();
        $model->is_active = $workflow->isActive();
        $model->created_at = $workflow->createdAt();
        $model->updated_at = $workflow->updatedAt();
        $model->deleted_at = $workflow->deletedAt();

        return $model;
    }
}
