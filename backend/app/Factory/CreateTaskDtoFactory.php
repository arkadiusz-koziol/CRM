<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CreateTaskDto;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\CreateTaskRequest;
use Illuminate\Auth\AuthManager;

class CreateTaskDtoFactory
{
    public function __construct(
        private readonly AuthManager $auth
    ) {}

    public function fromRequest(CreateTaskRequest $request): CreateTaskDto
    {
        return new CreateTaskDto(
            title: $request->get('title'),
            description: $request->get('description'),
            status: TaskStatus::from($request->get('status', TaskStatus::PENDING->value)),
            priority: TaskPriority::from($request->get('priority', TaskPriority::MEDIUM->value)),
            assignedTo: $request->get('assigned_to'),
            createdBy: $this->auth->id(),
            dueDate: $request->get('due_date'),
            estimatedHours: $request->get('estimated_hours')
        );
    }
}
