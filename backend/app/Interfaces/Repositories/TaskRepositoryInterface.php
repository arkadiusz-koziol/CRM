<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Dto\CreateTaskDto;
use App\Dto\UpdateTaskDto;
use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    /**
     * Create a new task.
     */
    public function create(CreateTaskDto $dto): Task;

    /**
     * Find a task by ID.
     */
    public function findById(int $id): ?Task;

    /**
     * Update a task.
     */
    public function update(Task $task, UpdateTaskDto $dto): Task;

    /**
     * Delete a task.
     */
    public function delete(Task $task): bool;

    /**
     * Get all tasks with pagination.
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get tasks by assigned user.
     */
    public function getByAssignedUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get tasks by creator user.
     */
    public function getByCreatorUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get tasks by status.
     */
    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get tasks by priority.
     */
    public function getByPriority(string $priority, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get overdue tasks.
     */
    public function getOverdueTasks(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get tasks due today.
     */
    public function getTasksDueToday(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get tasks due this week.
     */
    public function getTasksDueThisWeek(int $perPage = 15): LengthAwarePaginator;

    /**
     * Search tasks by title or description.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
}
