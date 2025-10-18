<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class WorkflowMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflows_table_has_correct_structure(): void
    {
        $this->assertTrue(Schema::hasTable('workflows'));

        $this->assertTrue(Schema::hasColumn('workflows', 'id'));
        $this->assertTrue(Schema::hasColumn('workflows', 'name'));
        $this->assertTrue(Schema::hasColumn('workflows', 'description'));
        $this->assertTrue(Schema::hasColumn('workflows', 'is_active'));
        $this->assertTrue(Schema::hasColumn('workflows', 'created_at'));
        $this->assertTrue(Schema::hasColumn('workflows', 'updated_at'));
        $this->assertTrue(Schema::hasColumn('workflows', 'deleted_at'));
    }

    public function test_workflow_rules_table_has_correct_structure(): void
    {
        $this->assertTrue(Schema::hasTable('workflow_rules'));

        $this->assertTrue(Schema::hasColumn('workflow_rules', 'id'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'workflow_id'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'name'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'description'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'conditions'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'actions'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'priority'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'is_active'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'created_at'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'updated_at'));
        $this->assertTrue(Schema::hasColumn('workflow_rules', 'deleted_at'));
    }

    public function test_workflow_actions_table_has_correct_structure(): void
    {
        $this->assertTrue(Schema::hasTable('workflow_actions'));

        $this->assertTrue(Schema::hasColumn('workflow_actions', 'id'));
        $this->assertTrue(Schema::hasColumn('workflow_actions', 'workflow_id'));
        $this->assertTrue(Schema::hasColumn('workflow_actions', 'name'));
        $this->assertTrue(Schema::hasColumn('workflow_actions', 'type'));
        $this->assertTrue(Schema::hasColumn('workflow_actions', 'config'));
        $this->assertTrue(Schema::hasColumn('workflow_actions', 'is_active'));
        $this->assertTrue(Schema::hasColumn('workflow_actions', 'created_at'));
        $this->assertTrue(Schema::hasColumn('workflow_actions', 'updated_at'));
        $this->assertTrue(Schema::hasColumn('workflow_actions', 'deleted_at'));
    }

    public function test_workflow_rules_foreign_key_constraint(): void
    {
        $workflow = \App\Models\Workflow::create([
            'name' => 'Test Workflow',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $rule = \App\Models\WorkflowRule::create([
            'workflow_id' => $workflow->id,
            'name' => 'Test Rule',
            'description' => 'Test Description',
            'conditions' => [],
            'actions' => [],
            'priority' => 0,
            'is_active' => true,
        ]);

        $this->assertEquals($workflow->id, $rule->workflow_id);
        $this->assertTrue($rule->workflow()->exists());
    }

    public function test_workflow_actions_foreign_key_constraint(): void
    {
        $workflow = \App\Models\Workflow::create([
            'name' => 'Test Workflow',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $action = \App\Models\WorkflowAction::create([
            'workflow_id' => $workflow->id,
            'name' => 'Test Action',
            'type' => 'create_task',
            'config' => [],
            'is_active' => true,
        ]);

        $this->assertEquals($workflow->id, $action->workflow_id);
        $this->assertTrue($action->workflow()->exists());
    }
}
