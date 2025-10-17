<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertEquals('pending', TaskStatus::PENDING->value);
        $this->assertEquals('in_progress', TaskStatus::IN_PROGRESS->value);
        $this->assertEquals('completed', TaskStatus::COMPLETED->value);
        $this->assertEquals('cancelled', TaskStatus::CANCELLED->value);
        $this->assertEquals('on_hold', TaskStatus::ON_HOLD->value);
    }

    public function test_enum_cases(): void
    {
        $cases = TaskStatus::cases();
        $this->assertCount(5, $cases);

        $expectedValues = [
            'pending',
            'in_progress',
            'completed',
            'cancelled',
            'on_hold',
        ];

        $actualValues = array_map(fn ($case) => $case->value, $cases);
        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_enum_from_value(): void
    {
        $this->assertEquals(TaskStatus::PENDING, TaskStatus::from('pending'));
        $this->assertEquals(TaskStatus::IN_PROGRESS, TaskStatus::from('in_progress'));
        $this->assertEquals(TaskStatus::COMPLETED, TaskStatus::from('completed'));
        $this->assertEquals(TaskStatus::CANCELLED, TaskStatus::from('cancelled'));
        $this->assertEquals(TaskStatus::ON_HOLD, TaskStatus::from('on_hold'));
    }

    public function test_enum_try_from_value(): void
    {
        $this->assertEquals(TaskStatus::PENDING, TaskStatus::tryFrom('pending'));
        $this->assertEquals(TaskStatus::IN_PROGRESS, TaskStatus::tryFrom('in_progress'));
        $this->assertEquals(TaskStatus::COMPLETED, TaskStatus::tryFrom('completed'));
        $this->assertEquals(TaskStatus::CANCELLED, TaskStatus::tryFrom('cancelled'));
        $this->assertEquals(TaskStatus::ON_HOLD, TaskStatus::tryFrom('on_hold'));
        $this->assertNull(TaskStatus::tryFrom('invalid_status'));
        $this->assertNull(TaskStatus::tryFrom(''));
    }

    public function test_enum_from_value_throws_exception_for_invalid_value(): void
    {
        $this->expectException(\ValueError::class);
        TaskStatus::from('invalid_status');
    }

    public function test_enum_from_value_throws_exception_for_empty_string(): void
    {
        $this->expectException(\ValueError::class);
        TaskStatus::from('');
    }

    public function test_enum_from_value_throws_exception_for_null(): void
    {
        $this->expectException(\TypeError::class);
        TaskStatus::from(null);
    }

    public function test_enum_name(): void
    {
        $this->assertEquals('PENDING', TaskStatus::PENDING->name);
        $this->assertEquals('IN_PROGRESS', TaskStatus::IN_PROGRESS->name);
        $this->assertEquals('COMPLETED', TaskStatus::COMPLETED->name);
        $this->assertEquals('CANCELLED', TaskStatus::CANCELLED->name);
        $this->assertEquals('ON_HOLD', TaskStatus::ON_HOLD->name);
    }

    public function test_enum_to_string(): void
    {
        $this->assertEquals('pending', TaskStatus::PENDING->value);
        $this->assertEquals('in_progress', TaskStatus::IN_PROGRESS->value);
        $this->assertEquals('completed', TaskStatus::COMPLETED->value);
        $this->assertEquals('cancelled', TaskStatus::CANCELLED->value);
        $this->assertEquals('on_hold', TaskStatus::ON_HOLD->value);
    }

    public function test_enum_equality(): void
    {
        $this->assertTrue(TaskStatus::PENDING === TaskStatus::PENDING);
        $this->assertTrue(TaskStatus::IN_PROGRESS === TaskStatus::IN_PROGRESS);
        $this->assertTrue(TaskStatus::COMPLETED === TaskStatus::COMPLETED);
        $this->assertTrue(TaskStatus::CANCELLED === TaskStatus::CANCELLED);
        $this->assertTrue(TaskStatus::ON_HOLD === TaskStatus::ON_HOLD);

        $this->assertFalse(TaskStatus::PENDING === TaskStatus::IN_PROGRESS);
        $this->assertFalse(TaskStatus::IN_PROGRESS === TaskStatus::COMPLETED);
        $this->assertFalse(TaskStatus::COMPLETED === TaskStatus::CANCELLED);
        $this->assertFalse(TaskStatus::CANCELLED === TaskStatus::ON_HOLD);
        $this->assertFalse(TaskStatus::ON_HOLD === TaskStatus::PENDING);
    }

    public function test_enum_inequality(): void
    {
        $this->assertTrue(TaskStatus::PENDING !== TaskStatus::IN_PROGRESS);
        $this->assertTrue(TaskStatus::IN_PROGRESS !== TaskStatus::COMPLETED);
        $this->assertTrue(TaskStatus::COMPLETED !== TaskStatus::CANCELLED);
        $this->assertTrue(TaskStatus::CANCELLED !== TaskStatus::ON_HOLD);
        $this->assertTrue(TaskStatus::ON_HOLD !== TaskStatus::PENDING);

        $this->assertFalse(TaskStatus::PENDING !== TaskStatus::PENDING);
        $this->assertFalse(TaskStatus::IN_PROGRESS !== TaskStatus::IN_PROGRESS);
        $this->assertFalse(TaskStatus::COMPLETED !== TaskStatus::COMPLETED);
        $this->assertFalse(TaskStatus::CANCELLED !== TaskStatus::CANCELLED);
        $this->assertFalse(TaskStatus::ON_HOLD !== TaskStatus::ON_HOLD);
    }

    public function test_enum_in_array(): void
    {
        $statuses = [
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
            TaskStatus::CANCELLED->value,
            TaskStatus::ON_HOLD->value,
        ];

        $this->assertContains(TaskStatus::PENDING->value, $statuses);
        $this->assertContains(TaskStatus::IN_PROGRESS->value, $statuses);
        $this->assertContains(TaskStatus::COMPLETED->value, $statuses);
        $this->assertContains(TaskStatus::CANCELLED->value, $statuses);
        $this->assertContains(TaskStatus::ON_HOLD->value, $statuses);
    }

    public function test_enum_array_keys(): void
    {
        $statuses = [
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
            TaskStatus::CANCELLED->value,
            TaskStatus::ON_HOLD->value,
        ];

        $this->assertArrayHasKey(0, $statuses);
        $this->assertArrayHasKey(1, $statuses);
        $this->assertArrayHasKey(2, $statuses);
        $this->assertArrayHasKey(3, $statuses);
        $this->assertArrayHasKey(4, $statuses);

        $this->assertArrayNotHasKey(5, $statuses);
    }

    public function test_enum_array_values(): void
    {
        $statuses = [
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
            TaskStatus::CANCELLED->value,
            TaskStatus::ON_HOLD->value,
        ];

        $this->assertEquals('pending', $statuses[0]);
        $this->assertEquals('in_progress', $statuses[1]);
        $this->assertEquals('completed', $statuses[2]);
        $this->assertEquals('cancelled', $statuses[3]);
        $this->assertEquals('on_hold', $statuses[4]);
    }

    public function test_enum_array_count(): void
    {
        $statuses = [
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
            TaskStatus::CANCELLED->value,
            TaskStatus::ON_HOLD->value,
        ];

        $this->assertCount(5, $statuses);
    }

    public function test_enum_array_types(): void
    {
        $statuses = [
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
            TaskStatus::CANCELLED->value,
            TaskStatus::ON_HOLD->value,
        ];

        $this->assertIsArray($statuses);

        foreach ($statuses as $status) {
            $this->assertIsString($status);
        }
    }

    public function test_enum_array_uniqueness(): void
    {
        $statuses = [
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
            TaskStatus::CANCELLED->value,
            TaskStatus::ON_HOLD->value,
        ];

        $this->assertEquals($statuses, array_unique($statuses));
    }

    public function test_enum_array_order(): void
    {
        $statuses = [
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
            TaskStatus::CANCELLED->value,
            TaskStatus::ON_HOLD->value,
        ];

        $this->assertEquals(['pending', 'in_progress', 'completed', 'cancelled', 'on_hold'], $statuses);
    }

    public function test_enum_case_sensitivity(): void
    {
        $this->assertNotEquals(TaskStatus::PENDING, TaskStatus::tryFrom('PENDING'));
        $this->assertNotEquals(TaskStatus::IN_PROGRESS, TaskStatus::tryFrom('IN_PROGRESS'));
        $this->assertNotEquals(TaskStatus::COMPLETED, TaskStatus::tryFrom('COMPLETED'));
        $this->assertNotEquals(TaskStatus::CANCELLED, TaskStatus::tryFrom('CANCELLED'));
        $this->assertNotEquals(TaskStatus::ON_HOLD, TaskStatus::tryFrom('ON_HOLD'));
    }

    public function test_enum_whitespace_handling(): void
    {
        $this->assertNotEquals(TaskStatus::PENDING, TaskStatus::tryFrom(' pending'));
        $this->assertNotEquals(TaskStatus::PENDING, TaskStatus::tryFrom('pending '));
        $this->assertNotEquals(TaskStatus::PENDING, TaskStatus::tryFrom(' pending '));
        $this->assertNotEquals(TaskStatus::PENDING, TaskStatus::tryFrom("\tpending"));
        $this->assertNotEquals(TaskStatus::PENDING, TaskStatus::tryFrom("pending\n"));
    }
}
