<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\CreateTaskDto;
use App\Dto\UpdateTaskDto;
use App\Interfaces\Repositories\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private readonly Task $model
    ) {}

    public function create(CreateTaskDto $dto): Task
    {
        $data = [
            'title' => $dto->getTitle(),
            'description' => $dto->getDescription(),
            'status' => $dto->getStatus(),
            'priority' => $dto->getPriority(),
            'assigned_to' => $dto->getAssignedTo(),
            'created_by' => $dto->getCreatedBy(),
        ];

        if ($dto->getDueDate()) {
            $data['due_date'] = Carbon::parse($dto->getDueDate());
        }

        if ($dto->getEstimatedHours()) {
            $data['estimated_hours'] = $dto->getEstimatedHours();
        }

        return $this->model->create($data);
    }

    public function findById(int $id): ?Task
    {
        return $this->model->with(['assignedTo', 'createdBy'])->find($id);
    }

    public function update(Task $task, UpdateTaskDto $dto): Task
    {
        $data = [];

        if ($dto->getTitle() !== null) {
            $data['title'] = $dto->getTitle();
        }

        if ($dto->getDescription() !== null) {
            $data['description'] = $dto->getDescription();
        }

        if ($dto->getStatus() !== null) {
            $data['status'] = $dto->getStatus();

            // Auto-set completed_at when status is completed
            if ($dto->getStatus()->value === 'completed') {
                $data['completed_at'] = Carbon::now();
            }
        }

        if ($dto->getPriority() !== null) {
            $data['priority'] = $dto->getPriority();
        }

        if ($dto->getAssignedTo() !== null) {
            $data['assigned_to'] = $dto->getAssignedTo();
        }

        if ($dto->getDueDate() !== null) {
            $data['due_date'] = Carbon::parse($dto->getDueDate());
        }

        if ($dto->getEstimatedHours() !== null) {
            $data['estimated_hours'] = $dto->getEstimatedHours();
        }

        if ($dto->getActualHours() !== null) {
            $data['actual_hours'] = $dto->getActualHours();
        }

        $task->update($data);

        return $task->fresh();
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByAssignedUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->where('assigned_to', $userId)
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate($perPage);
    }

    public function getByCreatorUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByPriority(string $priority, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->where('priority', $priority)
            ->orderBy('due_date', 'asc')
            ->paginate($perPage);
    }

    public function getOverdueTasks(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->where('due_date', '<', Carbon::now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('due_date', 'asc')
            ->paginate($perPage);
    }

    public function getTasksDueToday(int $perPage = 15): LengthAwarePaginator
    {
        $today = Carbon::today();

        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->whereDate('due_date', $today)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('priority', 'desc')
            ->paginate($perPage);
    }

    public function getTasksDueThisWeek(int $perPage = 15): LengthAwarePaginator
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->whereBetween('due_date', [$startOfWeek, $endOfWeek])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate($perPage);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['assignedTo', 'createdBy'])
            ->where(function (Builder $builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
