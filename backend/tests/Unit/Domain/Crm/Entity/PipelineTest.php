<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Crm\Entity;

use App\Domain\Crm\Entity\Pipeline;
use Carbon\Carbon;
use Tests\TestCase;

class PipelineTest extends TestCase
{
    public function test_it_creates_pipeline_with_required_fields(): void
    {
        $pipeline = Pipeline::create(
            name: 'Sales Pipeline',
            description: 'Main sales pipeline for CRM',
            isDefault: true,
            createdBy: 'user-123'
        );

        $this->assertInstanceOf(Pipeline::class, $pipeline);
        $this->assertNotEmpty($pipeline->id());
        $this->assertEquals('Sales Pipeline', $pipeline->name());
        $this->assertEquals('Main sales pipeline for CRM', $pipeline->description());
        $this->assertTrue($pipeline->isDefault());
        $this->assertEquals('user-123', $pipeline->createdBy());
        $this->assertInstanceOf(Carbon::class, $pipeline->createdAt());
        $this->assertInstanceOf(Carbon::class, $pipeline->updatedAt());
        $this->assertNull($pipeline->deletedAt());
        $this->assertFalse($pipeline->isDeleted());
    }

    public function test_it_creates_pipeline_with_minimal_fields(): void
    {
        $pipeline = Pipeline::create(
            name: 'Minimal Pipeline',
            description: null,
            isDefault: false,
            createdBy: 'user-456'
        );

        $this->assertInstanceOf(Pipeline::class, $pipeline);
        $this->assertEquals('Minimal Pipeline', $pipeline->name());
        $this->assertNull($pipeline->description());
        $this->assertFalse($pipeline->isDefault());
        $this->assertEquals('user-456', $pipeline->createdBy());
    }

    public function test_it_uses_uuid7_for_id(): void
    {
        $pipeline = Pipeline::create(
            name: 'Test Pipeline',
            description: null,
            isDefault: false,
            createdBy: 'user-789'
        );

        $id = $pipeline->id();
        $this->assertIsString($id);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id);
    }

    public function test_it_sets_created_and_updated_at_to_current_time(): void
    {
        $before = Carbon::now();

        $pipeline = Pipeline::create(
            name: 'Time Test Pipeline',
            description: null,
            isDefault: false,
            createdBy: 'user-time'
        );

        $after = Carbon::now();

        $this->assertTrue($pipeline->createdAt()->between($before, $after));
        $this->assertTrue($pipeline->updatedAt()->between($before, $after));
    }

    public function test_it_handles_boolean_default_flag(): void
    {
        $defaultPipeline = Pipeline::create(
            name: 'Default Pipeline',
            description: null,
            isDefault: true,
            createdBy: 'user-default'
        );

        $nonDefaultPipeline = Pipeline::create(
            name: 'Non-Default Pipeline',
            description: null,
            isDefault: false,
            createdBy: 'user-non-default'
        );

        $this->assertTrue($defaultPipeline->isDefault());
        $this->assertFalse($nonDefaultPipeline->isDefault());
    }
}
