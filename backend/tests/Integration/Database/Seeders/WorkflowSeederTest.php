<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class WorkflowSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_seeder_creates_workflows_and_rules(): void
    {
        $this->seed(\Database\Seeders\WorkflowSeeder::class);

        $workflows = Workflow::all();
        $this->assertGreaterThan(0, $workflows->count());

        $rules = WorkflowRule::all();
        $this->assertGreaterThan(0, $rules->count());

        // Check that we have the expected workflows
        $userFollowupWorkflow = $workflows->where('name', 'User Follow-up Automation')->first();
        $this->assertNotNull($userFollowupWorkflow);
        $this->assertEquals('Automated follow-up for inactive users', $userFollowupWorkflow->description);
        $this->assertTrue($userFollowupWorkflow->is_active);

        $opportunityWorkflow = $workflows->where('name', 'Opportunity Management')->first();
        $this->assertNotNull($opportunityWorkflow);
        $this->assertEquals('Automated opportunity management and follow-ups', $opportunityWorkflow->description);
        $this->assertTrue($opportunityWorkflow->is_active);

        // Check that we have the expected rules
        $inactiveUserRule = $rules->where('name', 'Inactive User Follow-up')->first();
        $this->assertNotNull($inactiveUserRule);
        $this->assertEquals($userFollowupWorkflow->id, $inactiveUserRule->workflow_id);
        $this->assertEquals(100, $inactiveUserRule->priority);
        $this->assertTrue($inactiveUserRule->is_active);

        $prospectWelcomeRule = $rules->where('name', 'Prospect Welcome')->first();
        $this->assertNotNull($prospectWelcomeRule);
        $this->assertEquals($userFollowupWorkflow->id, $prospectWelcomeRule->workflow_id);
        $this->assertEquals(90, $prospectWelcomeRule->priority);
        $this->assertTrue($prospectWelcomeRule->is_active);

        $demoReminderRule = $rules->where('name', 'Demo Reminder')->first();
        $this->assertNotNull($demoReminderRule);
        $this->assertEquals($opportunityWorkflow->id, $demoReminderRule->workflow_id);
        $this->assertEquals(80, $demoReminderRule->priority);
        $this->assertTrue($demoReminderRule->is_active);
    }

    public function test_workflow_rules_have_correct_conditions_and_actions(): void
    {
        $this->seed(\Database\Seeders\WorkflowSeeder::class);

        $inactiveUserRule = WorkflowRule::where('name', 'Inactive User Follow-up')->first();
        $this->assertNotNull($inactiveUserRule);

        $conditions = $inactiveUserRule->conditions;
        $this->assertEquals('and', $conditions['operator']);
        $this->assertCount(1, $conditions['conditions']);
        $this->assertEquals('user.last_login_at', $conditions['conditions'][0]['field']);
        $this->assertEquals('lt', $conditions['conditions'][0]['operator']);
        $this->assertEquals('30 days ago', $conditions['conditions'][0]['value']);

        $actions = $inactiveUserRule->actions;
        $this->assertCount(1, $actions);
        $this->assertEquals('create_task', $actions[0]['type']);
        $this->assertEquals('Follow up with inactive user', $actions[0]['config']['title']);
        $this->assertStringContainsString('30+ days', $actions[0]['config']['description']);

        $prospectWelcomeRule = WorkflowRule::where('name', 'Prospect Welcome')->first();
        $this->assertNotNull($prospectWelcomeRule);

        $conditions = $prospectWelcomeRule->conditions;
        $this->assertEquals('and', $conditions['operator']);
        $this->assertCount(1, $conditions['conditions']);
        $this->assertEquals('company.status', $conditions['conditions'][0]['field']);
        $this->assertEquals('eq', $conditions['conditions'][0]['operator']);
        $this->assertEquals('prospect', $conditions['conditions'][0]['value']);

        $actions = $prospectWelcomeRule->actions;
        $this->assertCount(1, $actions);
        $this->assertEquals('send_email', $actions[0]['type']);
        $this->assertEquals('Welcome to our platform!', $actions[0]['config']['subject']);
        $this->assertStringContainsString('Thank you for your interest', $actions[0]['config']['body']);

        $demoReminderRule = WorkflowRule::where('name', 'Demo Reminder')->first();
        $this->assertNotNull($demoReminderRule);

        $conditions = $demoReminderRule->conditions;
        $this->assertEquals('and', $conditions['operator']);
        $this->assertCount(1, $conditions['conditions']);
        $this->assertEquals('opportunity.stage_id', $conditions['conditions'][0]['field']);
        $this->assertEquals('eq', $conditions['conditions'][0]['operator']);
        $this->assertEquals('demo_stage_id', $conditions['conditions'][0]['value']);

        $actions = $demoReminderRule->actions;
        $this->assertCount(2, $actions);
        $this->assertEquals('create_task', $actions[0]['type']);
        $this->assertEquals('Demo preparation reminder', $actions[0]['config']['title']);
        $this->assertEquals('send_email', $actions[1]['type']);
        $this->assertEquals('Demo Reminder', $actions[1]['config']['subject']);
    }
}
