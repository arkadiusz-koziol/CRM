<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Domain\Activity\Entity\Activity;
use App\Interfaces\Repositories\ActivityRepositoryInterface;
use App\Services\ActivityService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class ActivityServiceTest extends TestCase
{
    private ActivityRepositoryInterface $activityRepository;

    private ActivityService $activityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityRepository = Mockery::mock(ActivityRepositoryInterface::class);
        $this->activityService = new ActivityService($this->activityRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_log_activity(): void
    {
        $action = 'user_created';
        $userName = 'John Doe';
        $userEmail = 'john.doe@example.com';
        $entityType = 'User';
        $entityId = '1';

        $this->activityRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Activity::class))
            ->andReturnUsing(function (Activity $activity) use ($action, $userName, $userEmail, $entityType, $entityId) {
                $this->assertEquals($action, $activity->action());
                $this->assertEquals($userName, $activity->userName());
                $this->assertEquals($userEmail, $activity->userEmail());
                $this->assertEquals($entityType, $activity->entityType());
                $this->assertEquals($entityId, $activity->entityId());
            });

        $this->activityService->logActivity($action, $userName, $userEmail, $entityType, $entityId);
    }

    public function test_log_activity_without_entity_id(): void
    {
        $action = 'system_started';
        $userName = 'System';
        $userEmail = 'system@example.com';
        $entityType = 'System';

        $this->activityRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Activity::class))
            ->andReturnUsing(function (Activity $activity) use ($action, $userName, $userEmail, $entityType) {
                $this->assertEquals($action, $activity->action());
                $this->assertEquals($userName, $activity->userName());
                $this->assertEquals($userEmail, $activity->userEmail());
                $this->assertEquals($entityType, $activity->entityType());
                $this->assertNull($activity->entityId());
            });

        $this->activityService->logActivity($action, $userName, $userEmail, $entityType);
    }

    public function test_get_recent_activities(): void
    {
        $limit = 5;
        $mockActivities = Collection::times($limit, fn () => $this->createActivityEntity());

        $this->activityRepository
            ->shouldReceive('getRecent')
            ->once()
            ->with($limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getRecentActivities($limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount($limit, $activities);
        $this->assertTrue($activities->every(fn ($activity) => $activity instanceof \App\Dto\ActivityDto));
    }

    public function test_get_activities_by_entity_type(): void
    {
        $entityType = 'User';
        $limit = 3;
        $mockActivities = Collection::times($limit, fn () => $this->createActivityEntity(['entityType' => $entityType]));

        $this->activityRepository
            ->shouldReceive('getByEntityType')
            ->once()
            ->with($entityType, $limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getActivitiesByEntityType($entityType, $limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount($limit, $activities);
        $this->assertTrue($activities->every(fn ($activity) => $activity instanceof \App\Dto\ActivityDto));
        $this->assertTrue($activities->every(fn ($activity) => $activity->getEntityType() === $entityType));
    }

    public function test_get_activities_by_entity_type_with_different_limits(): void
    {
        $entityType = 'Task';
        $limits = [1, 5, 10];

        foreach ($limits as $limit) {
            $mockActivities = Collection::times($limit, fn () => $this->createActivityEntity(['entityType' => $entityType]));

            $this->activityRepository = Mockery::mock(ActivityRepositoryInterface::class);
            $this->activityService = new ActivityService($this->activityRepository);

            $this->activityRepository
                ->shouldReceive('getByEntityType')
                ->once()
                ->with($entityType, $limit)
                ->andReturn($mockActivities);

            $activities = $this->activityService->getActivitiesByEntityType($entityType, $limit);

            $this->assertInstanceOf(Collection::class, $activities);
            $this->assertCount($limit, $activities);
        }
    }

    public function test_get_recent_activities_with_different_limits(): void
    {
        $limits = [0, 1, 5, 10];

        foreach ($limits as $limit) {
            $mockActivities = Collection::times($limit, fn () => $this->createActivityEntity());

            $this->activityRepository = Mockery::mock(ActivityRepositoryInterface::class);
            $this->activityService = new ActivityService($this->activityRepository);

            $this->activityRepository
                ->shouldReceive('getRecent')
                ->once()
                ->with($limit)
                ->andReturn($mockActivities);

            $activities = $this->activityService->getRecentActivities($limit);

            $this->assertInstanceOf(Collection::class, $activities);
            $this->assertCount($limit, $activities);
        }
    }

    public function test_get_activities_by_entity_type_with_empty_result(): void
    {
        $entityType = 'NonExistentEntity';
        $limit = 5;
        $mockActivities = Collection::make([]);

        $this->activityRepository
            ->shouldReceive('getByEntityType')
            ->once()
            ->with($entityType, $limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getActivitiesByEntityType($entityType, $limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount(0, $activities);
    }

    public function test_get_recent_activities_with_empty_result(): void
    {
        $limit = 5;
        $mockActivities = Collection::make([]);

        $this->activityRepository
            ->shouldReceive('getRecent')
            ->once()
            ->with($limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getRecentActivities($limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount(0, $activities);
    }

    public function test_log_activity_with_special_characters(): void
    {
        $action = '用户创建';
        $userName = '张三';
        $userEmail = 'zhangsan@example.com';
        $entityType = '用户';
        $entityId = '用户123';

        $this->activityRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Activity::class))
            ->andReturnUsing(function (Activity $activity) use ($action, $userName, $userEmail, $entityType, $entityId) {
                $this->assertEquals($action, $activity->action());
                $this->assertEquals($userName, $activity->userName());
                $this->assertEquals($userEmail, $activity->userEmail());
                $this->assertEquals($entityType, $activity->entityType());
                $this->assertEquals($entityId, $activity->entityId());
            });

        $this->activityService->logActivity($action, $userName, $userEmail, $entityType, $entityId);
    }

    public function test_log_activity_with_emoji_characters(): void
    {
        $action = '🎉 user_created';
        $userName = 'John 😊';
        $userEmail = 'john@example.com';
        $entityType = 'user 👤';
        $entityId = '123 🆔';

        $this->activityRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Activity::class))
            ->andReturnUsing(function (Activity $activity) use ($action, $userName, $userEmail, $entityType, $entityId) {
                $this->assertEquals($action, $activity->action());
                $this->assertEquals($userName, $activity->userName());
                $this->assertEquals($userEmail, $activity->userEmail());
                $this->assertEquals($entityType, $activity->entityType());
                $this->assertEquals($entityId, $activity->entityId());
            });

        $this->activityService->logActivity($action, $userName, $userEmail, $entityType, $entityId);
    }

    public function test_log_activity_with_very_long_strings(): void
    {
        $longString = str_repeat('a', 1000);

        $this->activityRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Activity::class))
            ->andReturnUsing(function (Activity $activity) use ($longString) {
                $this->assertEquals($longString, $activity->action());
                $this->assertEquals($longString, $activity->userName());
                $this->assertEquals($longString.'@example.com', $activity->userEmail());
                $this->assertEquals($longString, $activity->entityType());
                $this->assertEquals($longString, $activity->entityId());
            });

        $this->activityService->logActivity($longString, $longString, $longString.'@example.com', $longString, $longString);
    }

    public function test_log_activity_with_empty_strings(): void
    {
        $this->activityRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Activity::class))
            ->andReturnUsing(function (Activity $activity) {
                $this->assertEquals('', $activity->action());
                $this->assertEquals('', $activity->userName());
                $this->assertEquals('', $activity->userEmail());
                $this->assertEquals('', $activity->entityType());
                $this->assertEquals('', $activity->entityId());
            });

        $this->activityService->logActivity('', '', '', '', '');
    }

    public function test_log_activity_with_whitespace_only_strings(): void
    {
        $this->activityRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Activity::class))
            ->andReturnUsing(function (Activity $activity) {
                $this->assertEquals('   ', $activity->action());
                $this->assertEquals("\t\n", $activity->userName());
                $this->assertEquals('   ', $activity->userEmail());
                $this->assertEquals('   ', $activity->entityType());
                $this->assertEquals('   ', $activity->entityId());
            });

        $this->activityService->logActivity('   ', "\t\n", '   ', '   ', '   ');
    }

    public function test_get_activities_by_entity_type_with_different_entity_types(): void
    {
        $entityTypes = ['User', 'Task', 'Project', 'Organization', 'System'];
        $limit = 5;

        foreach ($entityTypes as $entityType) {
            $mockActivities = Collection::times($limit, fn () => $this->createActivityEntity(['entityType' => $entityType]));

            $this->activityRepository = Mockery::mock(ActivityRepositoryInterface::class);
            $this->activityService = new ActivityService($this->activityRepository);

            $this->activityRepository
                ->shouldReceive('getByEntityType')
                ->once()
                ->with($entityType, $limit)
                ->andReturn($mockActivities);

            $activities = $this->activityService->getActivitiesByEntityType($entityType, $limit);

            $this->assertInstanceOf(Collection::class, $activities);
            $this->assertCount($limit, $activities);
            $this->assertTrue($activities->every(fn ($activity) => $activity->getEntityType() === $entityType));
        }
    }

    public function test_get_activities_by_entity_type_with_zero_limit(): void
    {
        $entityType = 'User';
        $limit = 0;
        $mockActivities = Collection::make([]);

        $this->activityRepository
            ->shouldReceive('getByEntityType')
            ->once()
            ->with($entityType, $limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getActivitiesByEntityType($entityType, $limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount(0, $activities);
    }

    public function test_get_recent_activities_with_zero_limit(): void
    {
        $limit = 0;
        $mockActivities = Collection::make([]);

        $this->activityRepository
            ->shouldReceive('getRecent')
            ->once()
            ->with($limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getRecentActivities($limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount(0, $activities);
    }

    public function test_get_activities_by_entity_type_with_negative_limit(): void
    {
        $entityType = 'User';
        $limit = -1;
        $mockActivities = Collection::make([]);

        $this->activityRepository
            ->shouldReceive('getByEntityType')
            ->once()
            ->with($entityType, $limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getActivitiesByEntityType($entityType, $limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount(0, $activities);
    }

    public function test_get_recent_activities_with_negative_limit(): void
    {
        $limit = -1;
        $mockActivities = Collection::make([]);

        $this->activityRepository
            ->shouldReceive('getRecent')
            ->once()
            ->with($limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getRecentActivities($limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount(0, $activities);
    }

    public function test_get_activities_by_entity_type_with_very_large_limit(): void
    {
        $entityType = 'User';
        $limit = 1000;
        $mockActivities = Collection::times($limit, fn () => $this->createActivityEntity(['entityType' => $entityType]));

        $this->activityRepository
            ->shouldReceive('getByEntityType')
            ->once()
            ->with($entityType, $limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getActivitiesByEntityType($entityType, $limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount($limit, $activities);
    }

    public function test_get_recent_activities_with_very_large_limit(): void
    {
        $limit = 1000;
        $mockActivities = Collection::times($limit, fn () => $this->createActivityEntity());

        $this->activityRepository
            ->shouldReceive('getRecent')
            ->once()
            ->with($limit)
            ->andReturn($mockActivities);

        $activities = $this->activityService->getRecentActivities($limit);

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertCount($limit, $activities);
    }

    private function createActivityEntity(array $overrides = []): \App\Domain\Activity\Entity\Activity
    {
        $defaults = [
            'action' => 'test_action',
            'userName' => 'Test User',
            'userEmail' => 'test@example.com',
            'entityType' => 'TestEntity',
            'entityId' => '123',
        ];

        $data = array_merge($defaults, $overrides);

        return \App\Domain\Activity\Entity\Activity::create(
            action: $data['action'],
            userName: $data['userName'],
            userEmail: $data['userEmail'],
            entityType: $data['entityType'],
            entityId: $data['entityId']
        );
    }

    private function createActivityDto(array $overrides = []): \App\Dto\ActivityDto
    {
        $defaults = [
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'action' => 'test_action',
            'userName' => 'Test User',
            'userEmail' => 'test@example.com',
            'entityType' => 'TestEntity',
            'entityId' => '123',
            'createdAt' => '2024-01-01 12:00:00',
        ];

        $data = array_merge($defaults, $overrides);

        return new \App\Dto\ActivityDto(
            id: $data['id'],
            action: $data['action'],
            userName: $data['userName'],
            userEmail: $data['userEmail'],
            entityType: $data['entityType'],
            entityId: $data['entityId'],
            createdAt: \Carbon\Carbon::parse($data['createdAt'])
        );
    }
}
