<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Automation;

use App\Services\Automation\WorkflowActionExecutor;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class WorkflowActionExecutorTest extends TestCase
{
    private WorkflowActionExecutor $executor;

    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->executor = new WorkflowActionExecutor($this->logger);
    }

    public function test_it_executes_create_task_action(): void
    {
        $action = [
            'type' => 'create_task',
            'config' => [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'assignee_id' => 'user-123',
                'due_date' => '+7 days',
            ],
        ];

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Creating task via workflow', $this->isType('array'));

        $this->executor->execute($action);
    }

    public function test_it_executes_send_email_action(): void
    {
        $action = [
            'type' => 'send_email',
            'config' => [
                'to' => 'test@example.com',
                'subject' => 'Test Subject',
                'body' => 'Test Body',
            ],
        ];

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Sending email via workflow', $this->isType('array'));

        $this->executor->execute($action);
    }

    public function test_it_executes_send_sms_action(): void
    {
        $action = [
            'type' => 'send_sms',
            'config' => [
                'to' => '+1234567890',
                'message' => 'Test SMS',
            ],
        ];

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Sending SMS via workflow', $this->isType('array'));

        $this->executor->execute($action);
    }

    public function test_it_executes_add_tag_action(): void
    {
        $action = [
            'type' => 'add_tag',
            'config' => [
                'entity_type' => 'company',
                'entity_id' => 'company-123',
                'tag' => 'vip',
            ],
        ];

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Adding tag via workflow', $this->isType('array'));

        $this->executor->execute($action);
    }

    public function test_it_executes_assign_owner_action(): void
    {
        $action = [
            'type' => 'assign_owner',
            'config' => [
                'entity_type' => 'opportunity',
                'entity_id' => 'opp-123',
                'owner_id' => 'user-456',
            ],
        ];

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Assigning owner via workflow', $this->isType('array'));

        $this->executor->execute($action);
    }

    public function test_it_handles_unknown_action_type(): void
    {
        $action = [
            'type' => 'unknown_action',
            'config' => [],
        ];

        $this->logger->expects($this->once())
            ->method('warning')
            ->with('Unknown workflow action type', ['type' => 'unknown_action']);

        $this->executor->execute($action);
    }

    public function test_it_handles_missing_action_type(): void
    {
        $action = [
            'config' => [],
        ];

        $this->logger->expects($this->once())
            ->method('warning')
            ->with('Unknown workflow action type', ['type' => '']);

        $this->executor->execute($action);
    }
}
