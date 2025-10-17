<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Dto\CreateTaskDto;
use App\Dto\UpdateTaskDto;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Interfaces\Repositories\TaskRepositoryInterface;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    private TaskRepositoryInterface $taskRepository;
    private TaskService $taskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->taskService = new TaskService($this->taskRepository);
    }

    public function testCreateTask(): void
    {
        $dto = new CreateTaskDto(
            title: 'Test Task',
            description: 'Description',
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.0
        );
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($task);

        $result = $this->taskService->createTask($dto);

        $this->assertEquals($task, $result);
    }

    public function testGetTaskById(): void
    {
        $taskId = 1;
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn($task);

        $foundTask = $this->taskService->getTaskById($taskId);

        $this->assertEquals($task, $foundTask);
    }

    public function testUpdateTaskWithChanges(): void
    {
        $task = $this->createTask();
        $dto = new UpdateTaskDto(title: 'Updated Title');

        $this->taskRepository
            ->expects($this->once())
            ->method('update')
            ->with($task, $dto)
            ->willReturn($task);

        $updatedTask = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($task, $updatedTask);
    }

    public function testUpdateTaskNoChanges(): void
    {
        $task = $this->createTask();
        $dto = new UpdateTaskDto(); // No changes

        $this->taskRepository
            ->expects($this->never())
            ->method('update');

        $updatedTask = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($task, $updatedTask);
    }

    public function testDeleteTask(): void
    {
        $task = $this->createTask();

        $this->taskRepository
            ->expects($this->once())
            ->method('delete')
            ->with($task)
            ->willReturn(true);

        $result = $this->taskService->deleteTask($task);

        $this->assertTrue($result);
    }

    public function testGetAllTasks(): void
    {
        $perPage = 10;
        $paginator = $this->createMock(LengthAwarePaginator::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('getAllPaginated')
            ->with($perPage)
            ->willReturn($paginator);

        $result = $this->taskService->getAllTasks($perPage);

        $this->assertEquals($paginator, $result);
    }

    public function testGetTasksByAssignedUser(): void
    {
        $userId = 1;
        $perPage = 10;
        $paginator = $this->createMock(LengthAwarePaginator::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('getByAssignedUser')
            ->with($userId, $perPage)
            ->willReturn($paginator);

        $result = $this->taskService->getTasksByAssignedUser($userId, $perPage);

        $this->assertEquals($paginator, $result);
    }

    public function testGetTasksByCreatorUser(): void
    {
        $userId = 1;
        $perPage = 10;
        $paginator = $this->createMock(LengthAwarePaginator::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('getByCreatorUser')
            ->with($userId, $perPage)
            ->willReturn($paginator);

        $result = $this->taskService->getTasksByCreatorUser($userId, $perPage);

        $this->assertEquals($paginator, $result);
    }

    public function testGetTasksByStatus(): void
    {
        $status = TaskStatus::COMPLETED->value;
        $perPage = 10;
        $paginator = $this->createMock(LengthAwarePaginator::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('getByStatus')
            ->with($status, $perPage)
            ->willReturn($paginator);

        $result = $this->taskService->getTasksByStatus($status, $perPage);

        $this->assertEquals($paginator, $result);
    }

    public function testCreateTaskWithSpecialCharacters(): void
    {
        $dto = new CreateTaskDto(
            title: 'Tâche spéciale avec caractères accentués',
            description: 'Description avec émojis 🎉 et caractères spéciaux: àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ',
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.0
        );
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($task);

        $result = $this->taskService->createTask($dto);

        $this->assertEquals($task, $result);
    }

    public function testCreateTaskWithUnicodeCharacters(): void
    {
        $dto = new CreateTaskDto(
            title: '任务标题',
            description: '任务描述包含中文字符',
            status: TaskStatus::PENDING,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            createdBy: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.0
        );
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($task);

        $result = $this->taskService->createTask($dto);

        $this->assertEquals($task, $result);
    }

    public function testCreateTaskWithEmojiCharacters(): void
    {
        $dto = new CreateTaskDto(
            title: 'Task with emoji 🎯',
            description: 'Description with emojis 🚀 ✨ 💡',
            status: TaskStatus::PENDING,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            createdBy: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.0
        );
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($task);

        $result = $this->taskService->createTask($dto);

        $this->assertEquals($task, $result);
    }

    public function testCreateTaskWithVeryLongStrings(): void
    {
        $longString = str_repeat('a', 1000);
        
        $dto = new CreateTaskDto(
            title: $longString,
            description: $longString,
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.0
        );
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($task);

        $result = $this->taskService->createTask($dto);

        $this->assertEquals($task, $result);
    }

    public function testCreateTaskWithEmptyStrings(): void
    {
        $dto = new CreateTaskDto(
            title: '',
            description: '',
            status: TaskStatus::PENDING,
            priority: TaskPriority::LOW,
            assignedTo: 0,
            createdBy: 0,
            dueDate: '',
            estimatedHours: 0.0
        );
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($task);

        $result = $this->taskService->createTask($dto);

        $this->assertEquals($task, $result);
    }

    public function testCreateTaskWithWhitespaceOnlyStrings(): void
    {
        $dto = new CreateTaskDto(
            title: '   ',
            description: "\t\n",
            status: TaskStatus::PENDING,
            priority: TaskPriority::LOW,
            assignedTo: 0,
            createdBy: 0,
            dueDate: '   ',
            estimatedHours: 0.0
        );
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($task);

        $result = $this->taskService->createTask($dto);

        $this->assertEquals($task, $result);
    }

    public function testUpdateTaskWithSpecialCharacters(): void
    {
        $task = $this->createTask();
        $dto = new UpdateTaskDto(
            title: 'Tâche mise à jour avec caractères accentués',
            description: 'Description mise à jour avec émojis 🎉 et caractères spéciaux: àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ',
            status: TaskStatus::IN_PROGRESS,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.5,
            actualHours: 7.0
        );

        $this->taskRepository
            ->expects($this->once())
            ->method('update')
            ->with($task, $dto)
            ->willReturn($task);

        $updatedTask = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($task, $updatedTask);
    }

    public function testUpdateTaskWithUnicodeCharacters(): void
    {
        $task = $this->createTask();
        $dto = new UpdateTaskDto(
            title: '任务更新',
            description: '任务描述更新包含中文字符',
            status: TaskStatus::IN_PROGRESS,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.5,
            actualHours: 7.0
        );

        $this->taskRepository
            ->expects($this->once())
            ->method('update')
            ->with($task, $dto)
            ->willReturn($task);

        $updatedTask = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($task, $updatedTask);
    }

    public function testUpdateTaskWithEmojiCharacters(): void
    {
        $task = $this->createTask();
        $dto = new UpdateTaskDto(
            title: 'Task updated with emoji 🎯',
            description: 'Description updated with emojis 🚀 ✨ 💡',
            status: TaskStatus::IN_PROGRESS,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.5,
            actualHours: 7.0
        );

        $this->taskRepository
            ->expects($this->once())
            ->method('update')
            ->with($task, $dto)
            ->willReturn($task);

        $updatedTask = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($task, $updatedTask);
    }

    public function testUpdateTaskWithVeryLongStrings(): void
    {
        $task = $this->createTask();
        $longString = str_repeat('a', 1000);
        $dto = new UpdateTaskDto(
            title: $longString,
            description: $longString,
            status: TaskStatus::IN_PROGRESS,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.5,
            actualHours: 7.0
        );

        $this->taskRepository
            ->expects($this->once())
            ->method('update')
            ->with($task, $dto)
            ->willReturn($task);

        $updatedTask = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($task, $updatedTask);
    }

    public function testUpdateTaskWithEmptyStrings(): void
    {
        $task = $this->createTask();
        $dto = new UpdateTaskDto(
            title: '',
            description: '',
            status: TaskStatus::PENDING,
            priority: TaskPriority::LOW,
            assignedTo: 0,
            dueDate: '',
            estimatedHours: 0.0,
            actualHours: 0.0
        );

        $this->taskRepository
            ->expects($this->once())
            ->method('update')
            ->with($task, $dto)
            ->willReturn($task);

        $updatedTask = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($task, $updatedTask);
    }

    public function testUpdateTaskWithWhitespaceOnlyStrings(): void
    {
        $task = $this->createTask();
        $dto = new UpdateTaskDto(
            title: '   ',
            description: "\t\n",
            status: TaskStatus::PENDING,
            priority: TaskPriority::LOW,
            assignedTo: 0,
            dueDate: '   ',
            estimatedHours: 0.0,
            actualHours: 0.0
        );

        $this->taskRepository
            ->expects($this->once())
            ->method('update')
            ->with($task, $dto)
            ->willReturn($task);

        $updatedTask = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($task, $updatedTask);
    }

    public function testGetAllTasksWithDifferentPerPageValues(): void
    {
        $perPageValues = [1, 5, 10, 25, 50, 100, 1000];

        foreach ($perPageValues as $perPage) {
            $paginator = $this->createMock(LengthAwarePaginator::class);

            $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
            $this->taskService = new TaskService($this->taskRepository);

            $this->taskRepository
                ->expects($this->once())
                ->method('getAllPaginated')
                ->with($perPage)
                ->willReturn($paginator);

            $result = $this->taskService->getAllTasks($perPage);

            $this->assertEquals($paginator, $result);
        }
    }

    public function testGetTasksByAssignedUserWithDifferentPerPageValues(): void
    {
        $userId = 1;
        $perPageValues = [1, 5, 10, 25, 50, 100, 1000];

        foreach ($perPageValues as $perPage) {
            $paginator = $this->createMock(LengthAwarePaginator::class);

            $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
            $this->taskService = new TaskService($this->taskRepository);

            $this->taskRepository
                ->expects($this->once())
                ->method('getByAssignedUser')
                ->with($userId, $perPage)
                ->willReturn($paginator);

            $result = $this->taskService->getTasksByAssignedUser($userId, $perPage);

            $this->assertEquals($paginator, $result);
        }
    }

    public function testGetTasksByCreatorUserWithDifferentPerPageValues(): void
    {
        $userId = 1;
        $perPageValues = [1, 5, 10, 25, 50, 100, 1000];

        foreach ($perPageValues as $perPage) {
            $paginator = $this->createMock(LengthAwarePaginator::class);

            $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
            $this->taskService = new TaskService($this->taskRepository);

            $this->taskRepository
                ->expects($this->once())
                ->method('getByCreatorUser')
                ->with($userId, $perPage)
                ->willReturn($paginator);

            $result = $this->taskService->getTasksByCreatorUser($userId, $perPage);

            $this->assertEquals($paginator, $result);
        }
    }

    public function testGetTasksByStatusWithDifferentPerPageValues(): void
    {
        $status = TaskStatus::COMPLETED->value;
        $perPageValues = [1, 5, 10, 25, 50, 100, 1000];

        foreach ($perPageValues as $perPage) {
            $paginator = $this->createMock(LengthAwarePaginator::class);

            $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
            $this->taskService = new TaskService($this->taskRepository);

            $this->taskRepository
                ->expects($this->once())
                ->method('getByStatus')
                ->with($status, $perPage)
                ->willReturn($paginator);

            $result = $this->taskService->getTasksByStatus($status, $perPage);

            $this->assertEquals($paginator, $result);
        }
    }

    public function testGetTasksByAssignedUserWithDifferentUserIds(): void
    {
        $userIds = [0, 1, 2, 10, 100, 1000, 999999, 2147483647];
        $perPage = 10;

        foreach ($userIds as $userId) {
            $paginator = $this->createMock(LengthAwarePaginator::class);

            $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
            $this->taskService = new TaskService($this->taskRepository);

            $this->taskRepository
                ->expects($this->once())
                ->method('getByAssignedUser')
                ->with($userId, $perPage)
                ->willReturn($paginator);

            $result = $this->taskService->getTasksByAssignedUser($userId, $perPage);

            $this->assertEquals($paginator, $result);
        }
    }

    public function testGetTasksByCreatorUserWithDifferentUserIds(): void
    {
        $userIds = [0, 1, 2, 10, 100, 1000, 999999, 2147483647];
        $perPage = 10;

        foreach ($userIds as $userId) {
            $paginator = $this->createMock(LengthAwarePaginator::class);

            $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
            $this->taskService = new TaskService($this->taskRepository);

            $this->taskRepository
                ->expects($this->once())
                ->method('getByCreatorUser')
                ->with($userId, $perPage)
                ->willReturn($paginator);

            $result = $this->taskService->getTasksByCreatorUser($userId, $perPage);

            $this->assertEquals($paginator, $result);
        }
    }

    public function testGetTasksByStatusWithDifferentStatuses(): void
    {
        $statuses = [
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
            TaskStatus::CANCELLED->value,
            TaskStatus::ON_HOLD->value,
        ];
        $perPage = 10;

        foreach ($statuses as $status) {
            $paginator = $this->createMock(LengthAwarePaginator::class);

            $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
            $this->taskService = new TaskService($this->taskRepository);

            $this->taskRepository
                ->expects($this->once())
                ->method('getByStatus')
                ->with($status, $perPage)
                ->willReturn($paginator);

            $result = $this->taskService->getTasksByStatus($status, $perPage);

            $this->assertEquals($paginator, $result);
        }
    }

    public function testGetTaskByIdWithNonExistentId(): void
    {
        $nonExistentId = 999999;

        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($nonExistentId)
            ->willReturn(null);

        $foundTask = $this->taskService->getTaskById($nonExistentId);

        $this->assertNull($foundTask);
    }

    public function testDeleteTaskWithNonExistentTask(): void
    {
        $task = $this->createTask();

        $this->taskRepository
            ->expects($this->once())
            ->method('delete')
            ->with($task)
            ->willReturn(false);

        $result = $this->taskService->deleteTask($task);

        $this->assertFalse($result);
    }

    public function testUpdateTaskWithNonExistentTask(): void
    {
        $task = $this->createTask();
        $dto = new UpdateTaskDto(title: 'Updated Title');
        $updatedTask = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('update')
            ->with($task, $dto)
            ->willReturn($updatedTask);

        $result = $this->taskService->updateTask($task, $dto);

        $this->assertEquals($updatedTask, $result);
    }

    private function createTask(): Task
    {
        $task = $this->createMock(Task::class);
        $task->id = 1;
        $task->title = 'Test Task';
        $task->description = 'Test Description';
        $task->status = TaskStatus::PENDING->value;
        $task->priority = TaskPriority::MEDIUM->value;
        $task->assigned_to = 1;
        $task->created_by = 1;
        $task->due_date = '2024-12-31';
        $task->estimated_hours = 8.0;
        $task->actual_hours = 0.0;
        $task->created_at = '2024-01-01 12:00:00';
        $task->updated_at = '2024-01-01 12:00:00';

        return $task;
    }
}