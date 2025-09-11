<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateTaskDto;
use App\Dto\UpdateTaskDto;
use App\Interfaces\Repositories\TaskRepositoryInterface;
use App\Models\Task;
use App\Enums\TaskStatus;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Create a new task.
     */
    public function createTask(CreateTaskDto $dto): Task
    {
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

        return $this->taskRepository->update($task, $dto);
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): bool
    {
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
            status: TaskStatus::COMPLETED
        );

        return $this->taskRepository->update($task, $dto);
    }

    /**
     * Reassign a task to another user.
     */
    public function reassignTask(Task $task, int $newUserId): Task
    {
        $dto = new UpdateTaskDto(assignedTo: $newUserId);

        return $this->taskRepository->update($task, $dto);
    }
}
