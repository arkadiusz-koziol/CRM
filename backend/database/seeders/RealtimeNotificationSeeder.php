<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Services\Notification\RealtimeNotificationService;
use Illuminate\Database\Seeder;

final class RealtimeNotificationSeeder extends Seeder
{
    public function __construct(
        private RealtimeNotificationService $notificationService
    ) {}

    public function run(): void
    {
        if ($this->command) {
            $this->command->info('Seeding real-time notification demo data...');
        }

        // Create demo users
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
        ]);

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@crm.com',
        ]);

        $salesRep = User::factory()->create([
            'name' => 'Sales Rep',
            'email' => 'sales@crm.com',
        ]);

        $observer1 = User::factory()->create([
            'name' => 'Observer One',
            'email' => 'observer1@crm.com',
        ]);

        $observer2 = User::factory()->create([
            'name' => 'Observer Two',
            'email' => 'observer2@crm.com',
        ]);

        // Demo task assignment
        $this->notificationService->notifyTaskAssigned(
            'task-demo-001',
            (string) $salesRep->id,
            (string) $manager->id,
            'Follow up with prospect',
            'Contact the prospect about the demo scheduled for next week',
            '2024-12-31'
        );

        // Demo comment addition
        $this->notificationService->notifyCommentAdded(
            'comment-demo-001',
            'company',
            'company-demo-001',
            (string) $manager->id,
            $manager->name,
            'Great progress on this deal! Let\'s schedule a follow-up call.',
            [(string) $observer1->id, (string) $observer2->id]
        );

        // Demo status change
        $this->notificationService->notifyStatusChanged(
            'opportunity',
            'opportunity-demo-001',
            'prospecting',
            'demo_scheduled',
            (string) $salesRep->id,
            $salesRep->name,
            [(string) $observer1->id, (string) $observer2->id]
        );

        // Demo opportunity stage change
        $this->notificationService->notifyOpportunityStageChanged(
            'opportunity-demo-001',
            'stage-prospecting-001',
            'stage-demo-001',
            'Prospecting',
            'Demo Scheduled',
            (string) $salesRep->id,
            $salesRep->name,
            [(string) $observer1->id, (string) $observer2->id]
        );

        if ($this->command) {
            $this->command->info('Real-time notification demo data seeded successfully!');
            $this->command->info('Created users: Admin, Manager, Sales Rep, Observer One, Observer Two');
            $this->command->info('Demo events: Task assignment, Comment addition, Status change, Opportunity stage change');
        }
    }
}
