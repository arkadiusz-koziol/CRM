<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateTaskDto;
use App\Dto\UpdateTaskDto;
use App\Interfaces\Repositories\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Psr\Log\LoggerInterface;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Create a new task.
     */
    public function createTask(CreateTaskDto $dto): Task
    {
        $this->logger->info('Creating new task', [
            'title' => $dto->getTitle(),
            'assigned_to' => $dto->getAssignedTo(),
            'created_by' => $dto->getCreatedBy(),
        ]);

        return $this->taskRepository->create($dto);
    }

    /**
     * Get a task by ID.
     */
    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }

    /**
     * Update a task.
     */
    public function updateTask(Task $task, UpdateTaskDto $dto): Task
    {
        if (!$dto->hasChanges()) {
            return $task;
        }

        $this->logger->info('Updating task', [
            'task_id' => $task->getId(),
            'changes' => array_filter([
                'title' => $dto->getTitle(),
                'status' => $dto->getStatus()?->value,
                'priority' => $dto->getPriority()?->value,
                'assigned_to' => $dto->getAssignedTo(),
            ]),
        ]);

        return $this->taskRepository->update($task, $dto);
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): bool
    {
        $this->logger->info('Deleting task', [
            'task_id' => $task->getId(),
            'title' => $task->getTitle(),
        ]);

        return $this->taskRepository->delete($task);
    }

    /**
     * Get all tasks with pagination.
     */
    public function getAllTasks(int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getAllPaginated($perPage);
    }

    /**
     * Get tasks assigned to a specific user.
     */
    public function getTasksByAssignedUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getByAssignedUser($userId, $perPage);
    }

    /**
     * Get tasks created by a specific user.
     */
    public function getTasksByCreatorUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getByCreatorUser($userId, $perPage);
    }

    /**
     * Get tasks by status.
     */
    public function getTasksByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getByStatus($status, $perPage);
    }

    /**
     * Get tasks by priority.
     */
    public function getTasksByPriority(string $priority, int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getByPriority($priority, $perPage);
    }

    /**
     * Get overdue tasks.
     */
    public function getOverdueTasks(int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getOverdueTasks($perPage);
    }

    /**
     * Get tasks due today.
     */
    public function getTasksDueToday(int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getTasksDueToday($perPage);
    }

    /**
     * Get tasks due this week.
     */
    public function getTasksDueThisWeek(int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->getTasksDueThisWeek($perPage);
    }

    /**
     * Search tasks by title or description.
     */
    public function searchTasks(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepository->search($query, $perPage);
    }

    /**
     * Mark a task as completed.
     */
    public function markTaskAsCompleted(Task $task): Task
    {
        $dto = new UpdateTaskDto(
            status: \App\Enums\TaskStatus::COMPLETED,
            actualHours: $task->getEstimatedHours()
        );

        $this->logger->info('Marking task as completed', [
            'task_id' => $task->getId(),
            'title' => $task->getTitle(),
        ]);

        return $this->taskRepository->update($task, $dto);
    }

    /**
     * Reassign a task to another user.
     */
    public function reassignTask(Task $task, int $newUserId): Task
    {
        $dto = new UpdateTaskDto(assignedTo: $newUserId);

        $this->logger->info('Reassigning task', [
            'task_id' => $task->getId(),
            'title' => $task->getTitle(),
            'old_assigned_to' => $task->getAssignedTo(),
            'new_assigned_to' => $newUserId,
        ]);

        return $this->taskRepository->update($task, $dto);
    }
}
