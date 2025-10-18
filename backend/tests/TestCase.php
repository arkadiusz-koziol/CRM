<?php

declare(strict_types=1);

namespace Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Set deterministic time for all tests
        Carbon::setTestNow('2024-01-01 12:00:00');

        // Set deterministic UUID generation
        Uuid::setFactory(new \Ramsey\Uuid\UuidFactory);

        // Ensure permission tables exist before seeding
        $this->artisan('migrate', ['--path' => 'database/migrations/2024_11_01_232535_create_permission_tables.php']);

        // Seed permissions for tests
        $this->seedPermissions();

        // Fake external services
        Event::fake();
        Queue::fake();
        Cache::flush();
        Storage::fake('local');
        Mail::fake();
        Notification::fake();
        Bus::fake();
        Http::fake();
    }

    protected function tearDown(): void
    {
        // Reset time
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Create a user with specific attributes.
     */
    protected function createUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create($attributes);
    }

    /**
     * Create a task with specific attributes.
     */
    protected function createTask(array $attributes = []): \App\Models\Task
    {
        return \App\Models\Task::factory()->create($attributes);
    }

    /**
     * Create an activity entity.
     */
    protected function createActivity(array $attributes = []): \App\Domain\Activity\Entity\Activity
    {
        return \App\Domain\Activity\Entity\Activity::create(
            action: $attributes['action'] ?? 'test_action',
            userName: $attributes['userName'] ?? 'Test User',
            userEmail: $attributes['userEmail'] ?? 'test@example.com',
            entityType: $attributes['entityType'] ?? 'test_entity',
            entityId: $attributes['entityId'] ?? null
        );
    }

    /**
     * Assert that a job was dispatched.
     */
    protected function assertJobDispatched(string $jobClass, int $times = 1): void
    {
        Bus::assertDispatched($jobClass, $times);
    }

    /**
     * Assert that an event was dispatched.
     */
    protected function assertEventDispatched(string $eventClass, int $times = 1): void
    {
        Event::assertDispatched($eventClass, $times);
    }

    /**
     * Assert that a notification was sent.
     */
    protected function assertNotificationSent(string $notificationClass, int $times = 1): void
    {
        Notification::assertSentTo($this->user ?? null, $notificationClass, $times);
    }

    /**
     * Assert that an email was sent.
     */
    protected function assertEmailSent(string $mailableClass, int $times = 1): void
    {
        Mail::assertSent($mailableClass, $times);
    }

    /**
     * Assert that an HTTP request was made.
     */
    protected function assertHttpRequestMade(string $url, int $times = 1): void
    {
        Http::assertSent(function ($request) use ($url) {
            return str_contains($request->url(), $url);
        }, $times);
    }

    /**
     * Assert JSON structure matches expected format.
     */
    protected function assertJsonStructure(array $structure, array $data): void
    {
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('type', $data['data']);
        $this->assertArrayHasKey('id', $data['data']);
        $this->assertArrayHasKey('attributes', $data['data']);

        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                $this->assertArrayHasKey($key, $data['data']['attributes']);
                $this->assertJsonStructure($value, $data['data']['attributes'][$key]);
            } else {
                $this->assertArrayHasKey($key, $data['data']['attributes']);
            }
        }
    }

    /**
     * Assert that a model was created with specific attributes.
     */
    protected function assertModelCreated(string $modelClass, array $attributes): void
    {
        $this->assertDatabaseHas(
            (new $modelClass)->getTable(),
            $attributes
        );
    }

    /**
     * Assert that a model was updated with specific attributes.
     */
    protected function assertModelUpdated(string $modelClass, array $attributes, array $where): void
    {
        $this->assertDatabaseHas(
            (new $modelClass)->getTable(),
            array_merge($attributes, $where)
        );
    }

    /**
     * Assert that a model was soft deleted.
     */
    protected function assertModelSoftDeleted(string $modelClass, array $where): void
    {
        $this->assertSoftDeleted((new $modelClass)->getTable(), $where);
    }

    /**
     * Get test data for user creation.
     */
    protected function getUserData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+48123456789',
            'password' => 'password123',
            'city' => 'Warsaw',
            'vovoidship' => 'Mazowieckie',
        ], $overrides);
    }

    /**
     * Get test data for task creation.
     */
    protected function getTaskData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Test Task',
            'description' => 'Test task description',
            'status' => \App\Enums\TaskStatus::PENDING->value,
            'priority' => \App\Enums\TaskPriority::MEDIUM->value,
            'assigned_user_id' => null,
            'created_by_user_id' => null,
        ], $overrides);
    }

    /**
     * Assert that a response follows JSON:API format.
     */
    protected function assertJsonApiResponse(array $response): void
    {
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('type', $response['data']);
        $this->assertArrayHasKey('id', $response['data']);
        $this->assertArrayHasKey('attributes', $response['data']);
    }

    /**
     * Assert that a collection response follows JSON:API format.
     */
    protected function assertJsonApiCollectionResponse(array $response): void
    {
        $this->assertArrayHasKey('data', $response);
        $this->assertIsArray($response['data']);

        if (! empty($response['data'])) {
            $this->assertArrayHasKey('type', $response['data'][0]);
            $this->assertArrayHasKey('id', $response['data'][0]);
            $this->assertArrayHasKey('attributes', $response['data'][0]);
        }
    }

    /**
     * Seed permissions for tests.
     */
    protected function seedPermissions(): void
    {
        $permissions = [
            'city.create',
            'city.read',
            'city.show',
            'city.update',
            'city.delete',
            'city.list',
            'estate.create',
            'estate.read',
            'estate.show',
            'estate.update',
            'estate.delete',
            'estate.list',
            'material.create',
            'material.read',
            'material.show',
            'material.update',
            'material.delete',
            'material.list',
            'plan.create',
            'plan.read',
            'plan.show',
            'plan.update',
            'plan.delete',
            'plan.list',
            'task.create',
            'task.read',
            'task.show',
            'task.update',
            'task.delete',
            'task.list',
            'user.create',
            'user.read',
            'user.show',
            'user.update',
            'user.delete',
            'user.list',
            'car.list',
            'car.show',
            'car.create',
            'car.update',
            'car.delete',
            'training.user.remove',
            'training.user.add',
            'training.user.assign',
            'training.user.assign_all',
            'training.user.assign_by_role',
            'training.user.assign_selected',
            'training.user.list',
            'training.create',
            'training.read',
            'training.show',
            'training.update',
            'training.delete',
            'training.list',
            'training.file.attach',
            'training.file.list',
            'training.file.delete',
            'company.view',
            'company.create',
            'company.update',
            'company.delete',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
