<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm\Entity;

use App\Domain\Crm\Entity\Stage;
use Carbon\Carbon;
use Tests\TestCase;

class StageTest extends TestCase
{
    public function test_it_creates_stage_with_required_fields(): void
    {
        $stage = Stage::create(
            pipelineId: 'pipeline-123',
            name: 'Prospecting',
            description: 'Initial contact and qualification',
            order: 1,
            isFinal: false
        );

        $this->assertInstanceOf(Stage::class, $stage);
        $this->assertNotEmpty($stage->id());
        $this->assertEquals('pipeline-123', $stage->pipelineId());
        $this->assertEquals('Prospecting', $stage->name());
        $this->assertEquals('Initial contact and qualification', $stage->description());
        $this->assertEquals(1, $stage->order());
        $this->assertFalse($stage->isFinal());
        $this->assertInstanceOf(Carbon::class, $stage->createdAt());
        $this->assertInstanceOf(Carbon::class, $stage->updatedAt());
        $this->assertNull($stage->deletedAt());
        $this->assertFalse($stage->isDeleted());
    }

    public function test_it_creates_stage_with_minimal_fields(): void
    {
        $stage = Stage::create(
            pipelineId: 'pipeline-456',
            name: 'Demo',
            description: null,
            order: 2
        );

        $this->assertInstanceOf(Stage::class, $stage);
        $this->assertEquals('pipeline-456', $stage->pipelineId());
        $this->assertEquals('Demo', $stage->name());
        $this->assertNull($stage->description());
        $this->assertEquals(2, $stage->order());
        $this->assertFalse($stage->isFinal());
    }

    public function test_it_uses_uuid7_for_id(): void
    {
        $stage = Stage::create(
            pipelineId: 'pipeline-789',
            name: 'Test Stage',
            description: null,
            order: 3
        );

        $id = $stage->id();
        $this->assertIsString($id);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id);
    }

    public function test_it_sets_created_and_updated_at_to_current_time(): void
    {
        $before = Carbon::now();

        $stage = Stage::create(
            pipelineId: 'pipeline-time',
            name: 'Time Test Stage',
            description: null,
            order: 1
        );

        $after = Carbon::now();

        $this->assertTrue($stage->createdAt()->between($before, $after));
        $this->assertTrue($stage->updatedAt()->between($before, $after));
    }

    public function test_it_handles_final_stage_flag(): void
    {
        $finalStage = Stage::create(
            pipelineId: 'pipeline-final',
            name: 'Won',
            description: 'Opportunity successfully closed',
            order: 5,
            isFinal: true
        );

        $nonFinalStage = Stage::create(
            pipelineId: 'pipeline-non-final',
            name: 'Prospecting',
            description: 'Initial contact',
            order: 1,
            isFinal: false
        );

        $this->assertTrue($finalStage->isFinal());
        $this->assertFalse($nonFinalStage->isFinal());
    }

    public function test_it_handles_order_values(): void
    {
        $stages = [];
        for ($i = 1; $i <= 5; $i++) {
            $stages[] = Stage::create(
                pipelineId: 'pipeline-order',
                name: "Stage {$i}",
                description: null,
                order: $i
            );
        }

        foreach ($stages as $index => $stage) {
            $this->assertEquals($index + 1, $stage->order());
        }
    }
}
