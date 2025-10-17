<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Activity\Entity;

use App\Domain\Activity\Entity\Activity;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ActivityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2024-01-01 12:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_creates_activity_with_all_required_fields(): void
    {
        $activity = Activity::create(
            action: 'user_created',
            userName: 'John Doe',
            userEmail: 'john@example.com',
            entityType: 'user',
            entityId: '123'
        );

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $activity->id());
        $this->assertEquals('user_created', $activity->action());
        $this->assertEquals('John Doe', $activity->userName());
        $this->assertEquals('john@example.com', $activity->userEmail());
        $this->assertEquals('user', $activity->entityType());
        $this->assertEquals('123', $activity->entityId());
        $this->assertInstanceOf(Carbon::class, $activity->createdAt());
        $this->assertEquals('2024-01-01 12:00:00', $activity->createdAt()->toDateTimeString());
    }

    public function test_creates_activity_without_entity_id(): void
    {
        $activity = Activity::create(
            action: 'system_started',
            userName: 'System',
            userEmail: 'system@example.com',
            entityType: 'system'
        );

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $activity->id());
        $this->assertEquals('system_started', $activity->action());
        $this->assertEquals('System', $activity->userName());
        $this->assertEquals('system@example.com', $activity->userEmail());
        $this->assertEquals('system', $activity->entityType());
        $this->assertNull($activity->entityId());
        $this->assertInstanceOf(Carbon::class, $activity->createdAt());
    }

    public function test_generates_unique_ids_for_different_activities(): void
    {
        $activity1 = Activity::create(
            action: 'user_created',
            userName: 'John Doe',
            userEmail: 'john@example.com',
            entityType: 'user'
        );

        $activity2 = Activity::create(
            action: 'user_updated',
            userName: 'Jane Doe',
            userEmail: 'jane@example.com',
            entityType: 'user'
        );

        $this->assertNotEquals($activity1->id(), $activity2->id());
    }

    public function test_handles_special_characters_in_user_data(): void
    {
        $activity = Activity::create(
            action: 'user_created',
            userName: 'José María',
            userEmail: 'josé.maría@example.com',
            entityType: 'user',
            entityId: '123'
        );

        $this->assertEquals('José María', $activity->userName());
        $this->assertEquals('josé.maría@example.com', $activity->userEmail());
    }

    public function test_handles_empty_strings_in_optional_fields(): void
    {
        $activity = Activity::create(
            action: 'test_action',
            userName: '',
            userEmail: '',
            entityType: '',
            entityId: ''
        );

        $this->assertEquals('', $activity->userName());
        $this->assertEquals('', $activity->userEmail());
        $this->assertEquals('', $activity->entityType());
        $this->assertEquals('', $activity->entityId());
    }

    public function test_handles_very_long_strings(): void
    {
        $longString = str_repeat('a', 1000);

        $activity = Activity::create(
            action: $longString,
            userName: $longString,
            userEmail: $longString.'@example.com',
            entityType: $longString,
            entityId: $longString
        );

        $this->assertEquals($longString, $activity->action());
        $this->assertEquals($longString, $activity->userName());
        $this->assertEquals($longString.'@example.com', $activity->userEmail());
        $this->assertEquals($longString, $activity->entityType());
        $this->assertEquals($longString, $activity->entityId());
    }

    public function test_handles_unicode_characters(): void
    {
        $activity = Activity::create(
            action: '用户创建',
            userName: '张三',
            userEmail: 'zhangsan@example.com',
            entityType: '用户',
            entityId: '用户123'
        );

        $this->assertEquals('用户创建', $activity->action());
        $this->assertEquals('张三', $activity->userName());
        $this->assertEquals('zhangsan@example.com', $activity->userEmail());
        $this->assertEquals('用户', $activity->entityType());
        $this->assertEquals('用户123', $activity->entityId());
    }

    public function test_handles_emoji_characters(): void
    {
        $activity = Activity::create(
            action: '🎉 user_created',
            userName: 'John 😊',
            userEmail: 'john@example.com',
            entityType: 'user 👤',
            entityId: '123 🆔'
        );

        $this->assertEquals('🎉 user_created', $activity->action());
        $this->assertEquals('John 😊', $activity->userName());
        $this->assertEquals('john@example.com', $activity->userEmail());
        $this->assertEquals('user 👤', $activity->entityType());
        $this->assertEquals('123 🆔', $activity->entityId());
    }

    public function test_maintains_immutability(): void
    {
        $activity = Activity::create(
            action: 'user_created',
            userName: 'John Doe',
            userEmail: 'john@example.com',
            entityType: 'user',
            entityId: '123'
        );

        $originalId = $activity->id();
        $originalAction = $activity->action();
        $originalUserName = $activity->userName();
        $originalUserEmail = $activity->userEmail();
        $originalEntityType = $activity->entityType();
        $originalEntityId = $activity->entityId();
        $originalCreatedAt = $activity->createdAt();

        // Wait a moment to ensure time has passed
        Carbon::setTestNow('2024-01-01 12:01:00');

        // Create another activity
        $newActivity = Activity::create(
            action: 'user_updated',
            userName: 'Jane Doe',
            userEmail: 'jane@example.com',
            entityType: 'user',
            entityId: '456'
        );

        // Original activity should remain unchanged
        $this->assertEquals($originalId, $activity->id());
        $this->assertEquals($originalAction, $activity->action());
        $this->assertEquals($originalUserName, $activity->userName());
        $this->assertEquals($originalUserEmail, $activity->userEmail());
        $this->assertEquals($originalEntityType, $activity->entityType());
        $this->assertEquals($originalEntityId, $activity->entityId());
        $this->assertEquals($originalCreatedAt, $activity->createdAt());

        // New activity should have different values
        $this->assertNotEquals($originalId, $newActivity->id());
        $this->assertEquals('user_updated', $newActivity->action());
        $this->assertEquals('Jane Doe', $newActivity->userName());
        $this->assertEquals('jane@example.com', $newActivity->userEmail());
        $this->assertEquals('user', $newActivity->entityType());
        $this->assertEquals('456', $newActivity->entityId());
    }

    public function test_handles_different_time_zones(): void
    {
        // Test with different time zones
        $timezones = ['UTC', 'Europe/Warsaw', 'America/New_York', 'Asia/Tokyo'];

        foreach ($timezones as $timezone) {
            Carbon::setTestNow(Carbon::now($timezone));

            $activity = Activity::create(
                action: 'timezone_test',
                userName: 'Test User',
                userEmail: 'test@example.com',
                entityType: 'test'
            );

            $this->assertInstanceOf(Carbon::class, $activity->createdAt());
            // Carbon should use UTC in tests regardless of the timezone we set
            $this->assertEquals('UTC', $activity->createdAt()->timezone->getName());
        }
    }

    public function test_handles_edge_case_entity_ids(): void
    {
        $edgeCases = [
            '0',
            '1',
            '999999999',
            '000000000',
            '123-456-789',
            '123_456_789',
            '123.456.789',
            '123/456/789',
            '123\\456\\789',
            '123:456:789',
            '123;456;789',
            '123,456,789',
            '123 456 789',
            '123\t456\t789',
            '123\n456\n789',
            '123\r456\r789',
        ];

        foreach ($edgeCases as $entityId) {
            $activity = Activity::create(
                action: 'edge_case_test',
                userName: 'Test User',
                userEmail: 'test@example.com',
                entityType: 'test',
                entityId: $entityId
            );

            $this->assertEquals($entityId, $activity->entityId());
        }
    }
}
