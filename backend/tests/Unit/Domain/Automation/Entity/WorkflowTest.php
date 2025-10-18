<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Automation\Entity;

use App\Domain\Automation\Entity\Workflow;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

final class WorkflowTest extends TestCase
{
    public function test_it_can_create_workflow(): void
    {
        $workflow = Workflow::create('Test Workflow', 'Test Description');

        $this->assertInstanceOf(Workflow::class, $workflow);
        $this->assertNotEmpty($workflow->id());
        $this->assertEquals('Test Workflow', $workflow->name());
        $this->assertEquals('Test Description', $workflow->description());
        $this->assertTrue($workflow->isActive());
        $this->assertInstanceOf(Carbon::class, $workflow->createdAt());
        $this->assertInstanceOf(Carbon::class, $workflow->updatedAt());
        $this->assertNull($workflow->deletedAt());
    }

    public function test_it_can_activate_workflow(): void
    {
        $workflow = new Workflow(
            'test-id',
            'Test Workflow',
            'Test Description',
            false,
            Carbon::now(),
            Carbon::now()
        );

        $this->assertFalse($workflow->isActive());

        $workflow->activate();

        $this->assertTrue($workflow->isActive());
    }

    public function test_it_can_deactivate_workflow(): void
    {
        $workflow = new Workflow(
            'test-id',
            'Test Workflow',
            'Test Description',
            true,
            Carbon::now(),
            Carbon::now()
        );

        $this->assertTrue($workflow->isActive());

        $workflow->deactivate();

        $this->assertFalse($workflow->isActive());
    }

    public function test_it_can_update_details(): void
    {
        $workflow = Workflow::create('Original Name', 'Original Description');

        $workflow->updateDetails('Updated Name', 'Updated Description');

        $this->assertEquals('Updated Name', $workflow->name());
        $this->assertEquals('Updated Description', $workflow->description());
    }
}
