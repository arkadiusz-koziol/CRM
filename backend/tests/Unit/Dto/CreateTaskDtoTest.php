<?php

declare(strict_types=1);

namespace Tests\Unit\Dto;

use App\Dto\CreateTaskDto;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use PHPUnit\Framework\TestCase;

class CreateTaskDtoTest extends TestCase
{
    public function test_creates_dto_with_all_required_fields(): void
    {
        $dto = new CreateTaskDto(
            title: 'Test Task',
            description: 'This is a test task',
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 2,
            dueDate: '2024-12-31',
            estimatedHours: 8.5
        );

        $this->assertEquals('Test Task', $dto->getTitle());
        $this->assertEquals('This is a test task', $dto->getDescription());
        $this->assertEquals(TaskStatus::PENDING, $dto->getStatus());
        $this->assertEquals(TaskPriority::MEDIUM, $dto->getPriority());
        $this->assertEquals(1, $dto->getAssignedTo());
        $this->assertEquals(2, $dto->getCreatedBy());
        $this->assertEquals('2024-12-31', $dto->getDueDate());
        $this->assertEquals(8.5, $dto->getEstimatedHours());
    }

    public function test_creates_dto_with_null_optional_fields(): void
    {
        $dto = new CreateTaskDto(
            title: 'Test Task',
            description: 'This is a test task',
            status: TaskStatus::IN_PROGRESS,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            createdBy: 2,
            dueDate: null,
            estimatedHours: null
        );

        $this->assertEquals('Test Task', $dto->getTitle());
        $this->assertEquals('This is a test task', $dto->getDescription());
        $this->assertEquals(TaskStatus::IN_PROGRESS, $dto->getStatus());
        $this->assertEquals(TaskPriority::HIGH, $dto->getPriority());
        $this->assertEquals(1, $dto->getAssignedTo());
        $this->assertEquals(2, $dto->getCreatedBy());
        $this->assertNull($dto->getDueDate());
        $this->assertNull($dto->getEstimatedHours());
    }

    public function test_handles_empty_strings(): void
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

        $this->assertEquals('', $dto->getTitle());
        $this->assertEquals('', $dto->getDescription());
        $this->assertEquals(TaskStatus::PENDING, $dto->getStatus());
        $this->assertEquals(TaskPriority::LOW, $dto->getPriority());
        $this->assertEquals(0, $dto->getAssignedTo());
        $this->assertEquals(0, $dto->getCreatedBy());
        $this->assertEquals('', $dto->getDueDate());
        $this->assertEquals(0.0, $dto->getEstimatedHours());
    }

    public function test_handles_special_characters_in_title_and_description(): void
    {
        $dto = new CreateTaskDto(
            title: 'Tâche spéciale avec caractères accentués',
            description: 'Description avec émojis 🎉 et caractères spéciaux: àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ',
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 2,
            dueDate: '2024-12-31',
            estimatedHours: 8.5
        );

        $this->assertEquals('Tâche spéciale avec caractères accentués', $dto->getTitle());
        $this->assertEquals('Description avec émojis 🎉 et caractères spéciaux: àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ', $dto->getDescription());
    }

    public function test_handles_unicode_characters(): void
    {
        $dto = new CreateTaskDto(
            title: '任务标题',
            description: '任务描述包含中文字符',
            status: TaskStatus::PENDING,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            createdBy: 2,
            dueDate: '2024-12-31',
            estimatedHours: 8.5
        );

        $this->assertEquals('任务标题', $dto->getTitle());
        $this->assertEquals('任务描述包含中文字符', $dto->getDescription());
    }

    public function test_handles_emoji_characters(): void
    {
        $dto = new CreateTaskDto(
            title: 'Task with emoji 🎯',
            description: 'Description with emojis 🚀 ✨ 💡',
            status: TaskStatus::PENDING,
            priority: TaskPriority::HIGH,
            assignedTo: 1,
            createdBy: 2,
            dueDate: '2024-12-31',
            estimatedHours: 8.5
        );

        $this->assertEquals('Task with emoji 🎯', $dto->getTitle());
        $this->assertEquals('Description with emojis 🚀 ✨ 💡', $dto->getDescription());
    }

    public function test_handles_very_long_strings(): void
    {
        $longString = str_repeat('a', 1000);

        $dto = new CreateTaskDto(
            title: $longString,
            description: $longString,
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 2,
            dueDate: '2024-12-31',
            estimatedHours: 8.5
        );

        $this->assertEquals($longString, $dto->getTitle());
        $this->assertEquals($longString, $dto->getDescription());
    }

    public function test_handles_whitespace_only_strings(): void
    {
        $dto = new CreateTaskDto(
            title: '   ',
            description: "\t\n",
            status: TaskStatus::PENDING,
            priority: TaskPriority::LOW,
            assignedTo: 1,
            createdBy: 2,
            dueDate: '   ',
            estimatedHours: 0.0
        );

        $this->assertEquals('   ', $dto->getTitle());
        $this->assertEquals("\t\n", $dto->getDescription());
        $this->assertEquals('   ', $dto->getDueDate());
    }

    public function test_handles_all_task_statuses(): void
    {
        $statuses = [
            TaskStatus::PENDING,
            TaskStatus::IN_PROGRESS,
            TaskStatus::COMPLETED,
            TaskStatus::CANCELLED,
            TaskStatus::ON_HOLD,
        ];

        foreach ($statuses as $status) {
            $dto = new CreateTaskDto(
                title: 'Test Task',
                description: 'Test Description',
                status: $status,
                priority: TaskPriority::MEDIUM,
                assignedTo: 1,
                createdBy: 2,
                dueDate: '2024-12-31',
                estimatedHours: 8.5
            );

            $this->assertEquals($status, $dto->getStatus());
        }
    }

    public function test_handles_all_task_priorities(): void
    {
        $priorities = [
            TaskPriority::LOW,
            TaskPriority::MEDIUM,
            TaskPriority::HIGH,
            TaskPriority::URGENT,
        ];

        foreach ($priorities as $priority) {
            $dto = new CreateTaskDto(
                title: 'Test Task',
                description: 'Test Description',
                status: TaskStatus::PENDING,
                priority: $priority,
                assignedTo: 1,
                createdBy: 2,
                dueDate: '2024-12-31',
                estimatedHours: 8.5
            );

            $this->assertEquals($priority, $dto->getPriority());
        }
    }

    public function test_handles_different_due_date_formats(): void
    {
        $dateFormats = [
            '2024-12-31',
            '2024-01-01',
            '2024-06-15',
            '2024-02-29', // Leap year
            '2024-12-01',
            '2024-01-31',
            '2024-04-30',
            '2024-06-30',
            '2024-09-30',
            '2024-11-30',
        ];

        foreach ($dateFormats as $date) {
            $dto = new CreateTaskDto(
                title: 'Test Task',
                description: 'Test Description',
                status: TaskStatus::PENDING,
                priority: TaskPriority::MEDIUM,
                assignedTo: 1,
                createdBy: 2,
                dueDate: $date,
                estimatedHours: 8.5
            );

            $this->assertEquals($date, $dto->getDueDate());
        }
    }

    public function test_handles_different_estimated_hours_values(): void
    {
        $hoursValues = [
            0.0,
            0.5,
            1.0,
            1.5,
            2.0,
            4.0,
            8.0,
            8.5,
            16.0,
            24.0,
            40.0,
            80.0,
            168.0, // 1 week
            720.0, // 1 month
        ];

        foreach ($hoursValues as $hours) {
            $dto = new CreateTaskDto(
                title: 'Test Task',
                description: 'Test Description',
                status: TaskStatus::PENDING,
                priority: TaskPriority::MEDIUM,
                assignedTo: 1,
                createdBy: 2,
                dueDate: '2024-12-31',
                estimatedHours: $hours
            );

            $this->assertEquals($hours, $dto->getEstimatedHours());
        }
    }

    public function test_handles_different_user_id_values(): void
    {
        $userIdValues = [
            0,
            1,
            2,
            10,
            100,
            1000,
            999999,
            2147483647, // Max 32-bit integer
        ];

        foreach ($userIdValues as $userId) {
            $dto = new CreateTaskDto(
                title: 'Test Task',
                description: 'Test Description',
                status: TaskStatus::PENDING,
                priority: TaskPriority::MEDIUM,
                assignedTo: $userId,
                createdBy: $userId,
                dueDate: '2024-12-31',
                estimatedHours: 8.5
            );

            $this->assertEquals($userId, $dto->getAssignedTo());
            $this->assertEquals($userId, $dto->getCreatedBy());
        }
    }

    public function test_handles_null_due_date_when_other_fields_are_provided(): void
    {
        $dto = new CreateTaskDto(
            title: 'Test Task',
            description: 'Test Description',
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 2,
            dueDate: null,
            estimatedHours: 8.5
        );

        $this->assertEquals('Test Task', $dto->getTitle());
        $this->assertEquals('Test Description', $dto->getDescription());
        $this->assertEquals(TaskStatus::PENDING, $dto->getStatus());
        $this->assertEquals(TaskPriority::MEDIUM, $dto->getPriority());
        $this->assertEquals(1, $dto->getAssignedTo());
        $this->assertEquals(2, $dto->getCreatedBy());
        $this->assertNull($dto->getDueDate());
        $this->assertEquals(8.5, $dto->getEstimatedHours());
    }

    public function test_handles_null_estimated_hours_when_other_fields_are_provided(): void
    {
        $dto = new CreateTaskDto(
            title: 'Test Task',
            description: 'Test Description',
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 2,
            dueDate: '2024-12-31',
            estimatedHours: null
        );

        $this->assertEquals('Test Task', $dto->getTitle());
        $this->assertEquals('Test Description', $dto->getDescription());
        $this->assertEquals(TaskStatus::PENDING, $dto->getStatus());
        $this->assertEquals(TaskPriority::MEDIUM, $dto->getPriority());
        $this->assertEquals(1, $dto->getAssignedTo());
        $this->assertEquals(2, $dto->getCreatedBy());
        $this->assertEquals('2024-12-31', $dto->getDueDate());
        $this->assertNull($dto->getEstimatedHours());
    }

    public function test_maintains_immutability(): void
    {
        $dto = new CreateTaskDto(
            title: 'Original Task',
            description: 'Original Description',
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            createdBy: 2,
            dueDate: '2024-12-31',
            estimatedHours: 8.5
        );

        $originalTitle = $dto->getTitle();
        $originalDescription = $dto->getDescription();
        $originalStatus = $dto->getStatus();
        $originalPriority = $dto->getPriority();
        $originalAssignedTo = $dto->getAssignedTo();
        $originalCreatedBy = $dto->getCreatedBy();
        $originalDueDate = $dto->getDueDate();
        $originalEstimatedHours = $dto->getEstimatedHours();

        // Create a new DTO with different values
        $newDto = new CreateTaskDto(
            title: 'New Task',
            description: 'New Description',
            status: TaskStatus::IN_PROGRESS,
            priority: TaskPriority::HIGH,
            assignedTo: 3,
            createdBy: 4,
            dueDate: '2025-01-01',
            estimatedHours: 16.0
        );

        // Original DTO should remain unchanged
        $this->assertEquals($originalTitle, $dto->getTitle());
        $this->assertEquals($originalDescription, $dto->getDescription());
        $this->assertEquals($originalStatus, $dto->getStatus());
        $this->assertEquals($originalPriority, $dto->getPriority());
        $this->assertEquals($originalAssignedTo, $dto->getAssignedTo());
        $this->assertEquals($originalCreatedBy, $dto->getCreatedBy());
        $this->assertEquals($originalDueDate, $dto->getDueDate());
        $this->assertEquals($originalEstimatedHours, $dto->getEstimatedHours());

        // New DTO should have different values
        $this->assertEquals('New Task', $newDto->getTitle());
        $this->assertEquals('New Description', $newDto->getDescription());
        $this->assertEquals(TaskStatus::IN_PROGRESS, $newDto->getStatus());
        $this->assertEquals(TaskPriority::HIGH, $newDto->getPriority());
        $this->assertEquals(3, $newDto->getAssignedTo());
        $this->assertEquals(4, $newDto->getCreatedBy());
        $this->assertEquals('2025-01-01', $newDto->getDueDate());
        $this->assertEquals(16.0, $newDto->getEstimatedHours());
    }
}
