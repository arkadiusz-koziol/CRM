<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\UpdateTaskDto;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\UpdateTaskRequest;

class UpdateTaskDtoFactory
{
    public function fromRequest(UpdateTaskRequest $request): UpdateTaskDto
    {
        return new UpdateTaskDto(
            title: $request->get('title'),
            description: $request->get('description'),
            status: $request->has('status') ? TaskStatus::from($request->get('status')) : null,
            priority: $request->has('priority') ? TaskPriority::from($request->get('priority')) : null,
            assignedTo: $request->get('assigned_to'),
            dueDate: $request->get('due_date'),
            estimatedHours: $request->get('estimated_hours'),
            actualHours: $request->get('actual_hours')
        );
    }
}
