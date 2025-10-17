<?php

declare(strict_types=1);

namespace Tests\Unit\Dto;

use App\Dto\UpdateTaskDto;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use PHPUnit\Framework\TestCase;

class UpdateTaskDtoTest extends TestCase
{
    public function testCreatesDtoWithAllFields(): void
    {
        $dto = new UpdateTaskDto(
            title: 'Updated Task',
            description: 'Updated Description',
            status: TaskStatus::IN_PROGRESS,
            priority: TaskPriority::HIGH,
            assignedTo: 2,
            dueDate: '2024-12-31',
            estimatedHours: 10.0,
            actualHours: 8.5
        );

        $this->assertEquals('Updated Task', $dto->getTitle());
        $this->assertEquals('Updated Description', $dto->getDescription());
        $this->assertEquals(TaskStatus::IN_PROGRESS, $dto->getStatus());
        $this->assertEquals(TaskPriority::HIGH, $dto->getPriority());
        $this->assertEquals(2, $dto->getAssignedTo());
        $this->assertEquals('2024-12-31', $dto->getDueDate());
        $this->assertEquals(10.0, $dto->getEstimatedHours());
        $this->assertEquals(8.5, $dto->getActualHours());
        $this->assertTrue($dto->hasChanges());
    }

    public function testCreatesDtoWithPartialFields(): void
    {
        $dto = new UpdateTaskDto(
            title: 'Updated Task',
            status: TaskStatus::COMPLETED
        );

        $this->assertEquals('Updated Task', $dto->getTitle());
        $this->assertNull($dto->getDescription());
        $this->assertEquals(TaskStatus::COMPLETED, $dto->getStatus());
        $this->assertNull($dto->getPriority());
        $this->assertNull($dto->getAssignedTo());
        $this->assertNull($dto->getDueDate());
        $this->assertNull($dto->getEstimatedHours());
        $this->assertNull($dto->getActualHours());
        $this->assertTrue($dto->hasChanges());
    }

    public function testCreatesEmptyDto(): void
    {
        $dto = new UpdateTaskDto();

        $this->assertNull($dto->getTitle());
        $this->assertNull($dto->getDescription());
        $this->assertNull($dto->getStatus());
        $this->assertNull($dto->getPriority());
        $this->assertNull($dto->getAssignedTo());
        $this->assertNull($dto->getDueDate());
        $this->assertNull($dto->getEstimatedHours());
        $this->assertNull($dto->getActualHours());
        $this->assertFalse($dto->hasChanges());
    }

    public function testHandlesEmptyStrings(): void
    {
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

        $this->assertEquals('', $dto->getTitle());
        $this->assertEquals('', $dto->getDescription());
        $this->assertEquals(TaskStatus::PENDING, $dto->getStatus());
        $this->assertEquals(TaskPriority::LOW, $dto->getPriority());
        $this->assertEquals(0, $dto->getAssignedTo());
        $this->assertEquals('', $dto->getDueDate());
        $this->assertEquals(0.0, $dto->getEstimatedHours());
        $this->assertEquals(0.0, $dto->getActualHours());
        $this->assertTrue($dto->hasChanges());
    }

    public function testHandlesSpecialCharactersInTitleAndDescription(): void
    {
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

        $this->assertEquals('Tâche mise à jour avec caractères accentués', $dto->getTitle());
        $this->assertEquals('Description mise à jour avec émojis 🎉 et caractères spéciaux: àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ', $dto->getDescription());
    }

    public function testHandlesUnicodeCharacters(): void
    {
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

        $this->assertEquals('任务更新', $dto->getTitle());
        $this->assertEquals('任务描述更新包含中文字符', $dto->getDescription());
    }

    public function testHandlesEmojiCharacters(): void
    {
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

        $this->assertEquals('Task updated with emoji 🎯', $dto->getTitle());
        $this->assertEquals('Description updated with emojis 🚀 ✨ 💡', $dto->getDescription());
    }

    public function testHandlesVeryLongStrings(): void
    {
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

        $this->assertEquals($longString, $dto->getTitle());
        $this->assertEquals($longString, $dto->getDescription());
    }

    public function testHandlesWhitespaceOnlyStrings(): void
    {
        $dto = new UpdateTaskDto(
            title: '   ',
            description: "\t\n",
            status: TaskStatus::PENDING,
            priority: TaskPriority::LOW,
            assignedTo: 1,
            dueDate: '   ',
            estimatedHours: 0.0,
            actualHours: 0.0
        );

        $this->assertEquals('   ', $dto->getTitle());
        $this->assertEquals("\t\n", $dto->getDescription());
        $this->assertEquals('   ', $dto->getDueDate());
    }

    public function testHandlesAllTaskStatuses(): void
    {
        $statuses = [
            TaskStatus::PENDING,
            TaskStatus::IN_PROGRESS,
            TaskStatus::COMPLETED,
            TaskStatus::CANCELLED,
            TaskStatus::ON_HOLD,
        ];

        foreach ($statuses as $status) {
            $dto = new UpdateTaskDto(
                title: 'Updated Task',
                description: 'Updated Description',
                status: $status,
                priority: TaskPriority::MEDIUM,
                assignedTo: 1,
                dueDate: '2024-12-31',
                estimatedHours: 8.5,
                actualHours: 7.0
            );

            $this->assertEquals($status, $dto->getStatus());
        }
    }

    public function testHandlesAllTaskPriorities(): void
    {
        $priorities = [
            TaskPriority::LOW,
            TaskPriority::MEDIUM,
            TaskPriority::HIGH,
            TaskPriority::URGENT,
        ];

        foreach ($priorities as $priority) {
            $dto = new UpdateTaskDto(
                title: 'Updated Task',
                description: 'Updated Description',
                status: TaskStatus::IN_PROGRESS,
                priority: $priority,
                assignedTo: 1,
                dueDate: '2024-12-31',
                estimatedHours: 8.5,
                actualHours: 7.0
            );

            $this->assertEquals($priority, $dto->getPriority());
        }
    }

    public function testHandlesDifferentDueDateFormats(): void
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
            $dto = new UpdateTaskDto(
                title: 'Updated Task',
                description: 'Updated Description',
                status: TaskStatus::IN_PROGRESS,
                priority: TaskPriority::MEDIUM,
                assignedTo: 1,
                dueDate: $date,
                estimatedHours: 8.5,
                actualHours: 7.0
            );

            $this->assertEquals($date, $dto->getDueDate());
        }
    }

    public function testHandlesDifferentHoursValues(): void
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
            $dto = new UpdateTaskDto(
                title: 'Updated Task',
                description: 'Updated Description',
                status: TaskStatus::IN_PROGRESS,
                priority: TaskPriority::MEDIUM,
                assignedTo: 1,
                dueDate: '2024-12-31',
                estimatedHours: $hours,
                actualHours: $hours
            );

            $this->assertEquals($hours, $dto->getEstimatedHours());
            $this->assertEquals($hours, $dto->getActualHours());
        }
    }

    public function testHandlesDifferentUserIdValues(): void
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
            $dto = new UpdateTaskDto(
                title: 'Updated Task',
                description: 'Updated Description',
                status: TaskStatus::IN_PROGRESS,
                priority: TaskPriority::MEDIUM,
                assignedTo: $userId,
                dueDate: '2024-12-31',
                estimatedHours: 8.5,
                actualHours: 7.0
            );

            $this->assertEquals($userId, $dto->getAssignedTo());
        }
    }

    public function testHandlesNullFieldsWhenOtherFieldsAreProvided(): void
    {
        $dto = new UpdateTaskDto(
            title: 'Updated Task',
            description: null,
            status: TaskStatus::IN_PROGRESS,
            priority: null,
            assignedTo: null,
            dueDate: null,
            estimatedHours: null,
            actualHours: null
        );

        $this->assertEquals('Updated Task', $dto->getTitle());
        $this->assertNull($dto->getDescription());
        $this->assertEquals(TaskStatus::IN_PROGRESS, $dto->getStatus());
        $this->assertNull($dto->getPriority());
        $this->assertNull($dto->getAssignedTo());
        $this->assertNull($dto->getDueDate());
        $this->assertNull($dto->getEstimatedHours());
        $this->assertNull($dto->getActualHours());
        $this->assertTrue($dto->hasChanges());
    }

    public function testHandlesMixedNullAndNonNullFields(): void
    {
        $dto = new UpdateTaskDto(
            title: null,
            description: 'Updated Description',
            status: null,
            priority: TaskPriority::HIGH,
            assignedTo: null,
            dueDate: '2024-12-31',
            estimatedHours: null,
            actualHours: 8.0
        );

        $this->assertNull($dto->getTitle());
        $this->assertEquals('Updated Description', $dto->getDescription());
        $this->assertNull($dto->getStatus());
        $this->assertEquals(TaskPriority::HIGH, $dto->getPriority());
        $this->assertNull($dto->getAssignedTo());
        $this->assertEquals('2024-12-31', $dto->getDueDate());
        $this->assertNull($dto->getEstimatedHours());
        $this->assertEquals(8.0, $dto->getActualHours());
        $this->assertTrue($dto->hasChanges());
    }

    public function testMaintainsImmutability(): void
    {
        $dto = new UpdateTaskDto(
            title: 'Original Task',
            description: 'Original Description',
            status: TaskStatus::PENDING,
            priority: TaskPriority::MEDIUM,
            assignedTo: 1,
            dueDate: '2024-12-31',
            estimatedHours: 8.5,
            actualHours: 7.0
        );

        $originalTitle = $dto->getTitle();
        $originalDescription = $dto->getDescription();
        $originalStatus = $dto->getStatus();
        $originalPriority = $dto->getPriority();
        $originalAssignedTo = $dto->getAssignedTo();
        $originalDueDate = $dto->getDueDate();
        $originalEstimatedHours = $dto->getEstimatedHours();
        $originalActualHours = $dto->getActualHours();

        // Create a new DTO with different values
        $newDto = new UpdateTaskDto(
            title: 'New Task',
            description: 'New Description',
            status: TaskStatus::COMPLETED,
            priority: TaskPriority::HIGH,
            assignedTo: 2,
            dueDate: '2025-01-01',
            estimatedHours: 16.0,
            actualHours: 15.0
        );

        // Original DTO should remain unchanged
        $this->assertEquals($originalTitle, $dto->getTitle());
        $this->assertEquals($originalDescription, $dto->getDescription());
        $this->assertEquals($originalStatus, $dto->getStatus());
        $this->assertEquals($originalPriority, $dto->getPriority());
        $this->assertEquals($originalAssignedTo, $dto->getAssignedTo());
        $this->assertEquals($originalDueDate, $dto->getDueDate());
        $this->assertEquals($originalEstimatedHours, $dto->getEstimatedHours());
        $this->assertEquals($originalActualHours, $dto->getActualHours());

        // New DTO should have different values
        $this->assertEquals('New Task', $newDto->getTitle());
        $this->assertEquals('New Description', $newDto->getDescription());
        $this->assertEquals(TaskStatus::COMPLETED, $newDto->getStatus());
        $this->assertEquals(TaskPriority::HIGH, $newDto->getPriority());
        $this->assertEquals(2, $newDto->getAssignedTo());
        $this->assertEquals('2025-01-01', $newDto->getDueDate());
        $this->assertEquals(16.0, $newDto->getEstimatedHours());
        $this->assertEquals(15.0, $newDto->getActualHours());
    }
}