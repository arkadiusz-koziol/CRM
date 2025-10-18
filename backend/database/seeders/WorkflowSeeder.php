<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Automation\Entity\Workflow;
use App\Services\Automation\WorkflowService;
use Illuminate\Database\Seeder;

final class WorkflowSeeder extends Seeder
{
    public function __construct(
        private WorkflowService $workflowService
    ) {}

    public function run(): void
    {
        // Create User Follow-up Workflow
        $workflowId = $this->workflowService->createWorkflow(
            'User Follow-up Automation',
            'Automated follow-up for inactive users'
        );

        // Rule 1: User last login > 30 days → Create follow-up task
        $this->workflowService->createRule(
            $workflowId,
            'Inactive User Follow-up',
            'Create follow-up task for users who haven\'t logged in for 30+ days',
            [
                'operator' => 'and',
                'conditions' => [
                    [
                        'field' => 'user.last_login_at',
                        'operator' => 'lt',
                        'value' => '30 days ago',
                    ],
                ],
            ],
            [
                [
                    'type' => 'create_task',
                    'config' => [
                        'title' => 'Follow up with inactive user',
                        'description' => 'User has not logged in for 30+ days. Schedule follow-up call or email.',
                        'assignee_id' => null,
                        'due_date' => '+7 days',
                    ],
                ],
            ],
            100
        );

        // Rule 2: Company status = prospect → Send welcome email
        $this->workflowService->createRule(
            $workflowId,
            'Prospect Welcome',
            'Send welcome email to new prospects',
            [
                'operator' => 'and',
                'conditions' => [
                    [
                        'field' => 'company.status',
                        'operator' => 'eq',
                        'value' => 'prospect',
                    ],
                ],
            ],
            [
                [
                    'type' => 'send_email',
                    'config' => [
                        'to' => 'company.contact_email',
                        'subject' => 'Welcome to our platform!',
                        'body' => 'Thank you for your interest. We look forward to working with you.',
                    ],
                ],
            ],
            90
        );

        // Create Opportunity Management Workflow
        $opportunityWorkflowId = $this->workflowService->createWorkflow(
            'Opportunity Management',
            'Automated opportunity management and follow-ups'
        );

        // Rule 3: Opportunity stage = demo → Schedule demo reminder
        $this->workflowService->createRule(
            $opportunityWorkflowId,
            'Demo Reminder',
            'Send reminder for scheduled demos',
            [
                'operator' => 'and',
                'conditions' => [
                    [
                        'field' => 'opportunity.stage_id',
                        'operator' => 'eq',
                        'value' => 'demo_stage_id',
                    ],
                ],
            ],
            [
                [
                    'type' => 'create_task',
                    'config' => [
                        'title' => 'Demo preparation reminder',
                        'description' => 'Prepare demo materials and confirm meeting details.',
                        'assignee_id' => 'opportunity.owner_id',
                        'due_date' => '+1 day',
                    ],
                ],
                [
                    'type' => 'send_email',
                    'config' => [
                        'to' => 'opportunity.contact_email',
                        'subject' => 'Demo Reminder',
                        'body' => 'This is a reminder about your upcoming demo session.',
                    ],
                ],
            ],
            80
        );
    }
}
