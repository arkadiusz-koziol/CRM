<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Automation\Entity;

use App\Domain\Automation\Entity\WorkflowRule;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

final class WorkflowRuleTest extends TestCase
{
    public function test_it_can_create_workflow_rule(): void
    {
        $conditions = [
            'operator' => 'and',
            'conditions' => [
                [
                    'field' => 'user.last_login_at',
                    'operator' => 'lt',
                    'value' => '30 days ago',
                ],
            ],
        ];

        $actions = [
            [
                'type' => 'create_task',
                'config' => [
                    'title' => 'Follow up with user',
                    'description' => 'User has been inactive',
                ],
            ],
        ];

        $rule = WorkflowRule::create(
            'workflow-id',
            'Test Rule',
            'Test Description',
            $conditions,
            $actions,
            100
        );

        $this->assertInstanceOf(WorkflowRule::class, $rule);
        $this->assertNotEmpty($rule->id());
        $this->assertEquals('workflow-id', $rule->workflowId());
        $this->assertEquals('Test Rule', $rule->name());
        $this->assertEquals('Test Description', $rule->description());
        $this->assertEquals($conditions, $rule->conditions());
        $this->assertEquals($actions, $rule->actions());
        $this->assertEquals(100, $rule->priority());
        $this->assertTrue($rule->isActive());
        $this->assertInstanceOf(Carbon::class, $rule->createdAt());
        $this->assertInstanceOf(Carbon::class, $rule->updatedAt());
        $this->assertNull($rule->deletedAt());
    }

    public function test_it_can_activate_rule(): void
    {
        $rule = new WorkflowRule(
            'test-id',
            'workflow-id',
            'Test Rule',
            'Test Description',
            [],
            [],
            0,
            false,
            Carbon::now(),
            Carbon::now()
        );

        $this->assertFalse($rule->isActive());

        $rule->activate();

        $this->assertTrue($rule->isActive());
    }

    public function test_it_can_deactivate_rule(): void
    {
        $rule = new WorkflowRule(
            'test-id',
            'workflow-id',
            'Test Rule',
            'Test Description',
            [],
            [],
            0,
            true,
            Carbon::now(),
            Carbon::now()
        );

        $this->assertTrue($rule->isActive());

        $rule->deactivate();

        $this->assertFalse($rule->isActive());
    }

    public function test_it_can_update_rule_details(): void
    {
        $rule = WorkflowRule::create(
            'workflow-id',
            'Original Name',
            'Original Description',
            [],
            [],
            0
        );

        $newConditions = [
            'operator' => 'or',
            'conditions' => [],
        ];

        $newActions = [
            [
                'type' => 'send_email',
                'config' => ['to' => 'test@example.com'],
            ],
        ];

        $rule->updateDetails(
            'Updated Name',
            'Updated Description',
            $newConditions,
            $newActions,
            200
        );

        $this->assertEquals('Updated Name', $rule->name());
        $this->assertEquals('Updated Description', $rule->description());
        $this->assertEquals($newConditions, $rule->conditions());
        $this->assertEquals($newActions, $rule->actions());
        $this->assertEquals(200, $rule->priority());
    }
}
