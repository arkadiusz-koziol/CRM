<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Seeders;

use App\Events\CommentAdded;
use App\Events\OpportunityStageChanged;
use App\Events\StatusChanged;
use App\Events\TaskAssigned;
use App\Models\User;
use Database\Seeders\RealtimeNotificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class RealtimeNotificationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_demo_users(): void
    {
        Event::fake();

        $seeder = new RealtimeNotificationSeeder(app(\App\Services\Notification\RealtimeNotificationService::class));
        $seeder->run();

        $this->assertDatabaseHas('users', [
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Manager User',
            'email' => 'manager@crm.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Sales Rep',
            'email' => 'sales@crm.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Observer One',
            'email' => 'observer1@crm.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Observer Two',
            'email' => 'observer2@crm.com',
        ]);
    }

    public function test_it_dispatches_demo_events(): void
    {
        Event::fake();

        $seeder = new RealtimeNotificationSeeder(app(\App\Services\Notification\RealtimeNotificationService::class));
        $seeder->run();

        // Check task assignment event
        Event::assertDispatched(TaskAssigned::class, function ($event) {
            return $event->taskId === 'task-demo-001' &&
                   $event->taskTitle === 'Follow up with prospect' &&
                   $event->taskDescription === 'Contact the prospect about the demo scheduled for next week' &&
                   $event->dueDate === '2024-12-31';
        });

        // Check comment added event
        Event::assertDispatched(CommentAdded::class, function ($event) {
            return $event->commentId === 'comment-demo-001' &&
                   $event->entityType === 'company' &&
                   $event->entityId === 'company-demo-001' &&
                   $event->comment === 'Great progress on this deal! Let\'s schedule a follow-up call.';
        });

        // Check status changed event
        Event::assertDispatched(StatusChanged::class, function ($event) {
            return $event->entityType === 'opportunity' &&
                   $event->entityId === 'opportunity-demo-001' &&
                   $event->oldStatus === 'prospecting' &&
                   $event->newStatus === 'demo_scheduled';
        });

        // Check opportunity stage changed event
        Event::assertDispatched(OpportunityStageChanged::class, function ($event) {
            return $event->opportunityId === 'opportunity-demo-001' &&
                   $event->oldStageId === 'stage-prospecting-001' &&
                   $event->newStageId === 'stage-demo-001' &&
                   $event->oldStageName === 'Prospecting' &&
                   $event->newStageName === 'Demo Scheduled';
        });
    }

    public function test_it_creates_correct_number_of_users(): void
    {
        Event::fake();

        $seeder = new RealtimeNotificationSeeder(app(\App\Services\Notification\RealtimeNotificationService::class));
        $seeder->run();

        $this->assertEquals(5, User::count());
    }

    public function test_it_handles_observers_correctly(): void
    {
        Event::fake();

        $seeder = new RealtimeNotificationSeeder(app(\App\Services\Notification\RealtimeNotificationService::class));
        $seeder->run();

        // Get the created users
        $observer1 = User::where('email', 'observer1@crm.com')->first();
        $observer2 = User::where('email', 'observer2@crm.com')->first();

        $this->assertNotNull($observer1);
        $this->assertNotNull($observer2);

        // Check that observers are included in events
        Event::assertDispatched(CommentAdded::class, function ($event) use ($observer1, $observer2) {
            return in_array($observer1->id, $event->observers) &&
                   in_array($observer2->id, $event->observers);
        });

        Event::assertDispatched(StatusChanged::class, function ($event) use ($observer1, $observer2) {
            return in_array($observer1->id, $event->observers) &&
                   in_array($observer2->id, $event->observers);
        });

        Event::assertDispatched(OpportunityStageChanged::class, function ($event) use ($observer1, $observer2) {
            return in_array($observer1->id, $event->observers) &&
                   in_array($observer2->id, $event->observers);
        });
    }
}
