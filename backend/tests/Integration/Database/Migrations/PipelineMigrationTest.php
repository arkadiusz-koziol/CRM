<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Migrations;

use App\Models\Pipeline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PipelineMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipelines_table_has_correct_structure(): void
    {
        $this->assertTrue(\Schema::hasTable('pipelines'));
        $this->assertTrue(\Schema::hasColumn('pipelines', 'id'));
        $this->assertTrue(\Schema::hasColumn('pipelines', 'name'));
        $this->assertTrue(\Schema::hasColumn('pipelines', 'description'));
        $this->assertTrue(\Schema::hasColumn('pipelines', 'is_default'));
        $this->assertTrue(\Schema::hasColumn('pipelines', 'created_by'));
        $this->assertTrue(\Schema::hasColumn('pipelines', 'created_at'));
        $this->assertTrue(\Schema::hasColumn('pipelines', 'updated_at'));
        $this->assertTrue(\Schema::hasColumn('pipelines', 'deleted_at'));
    }

    public function test_pipelines_table_accepts_valid_data(): void
    {
        $user = User::factory()->create();

        $pipeline = Pipeline::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Sales Pipeline',
            'description' => 'Main sales pipeline for CRM',
            'is_default' => true,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('pipelines', [
            'name' => 'Sales Pipeline',
            'description' => 'Main sales pipeline for CRM',
            'is_default' => true,
            'created_by' => $user->id,
        ]);
    }

    public function test_pipelines_table_accepts_nullable_description(): void
    {
        $user = User::factory()->create();

        $pipeline = Pipeline::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Minimal Pipeline',
            'description' => null,
            'is_default' => false,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('pipelines', [
            'name' => 'Minimal Pipeline',
            'description' => null,
            'is_default' => false,
        ]);
    }

    public function test_pipelines_table_soft_deletes(): void
    {
        $user = User::factory()->create();

        $pipeline = Pipeline::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Soft Delete Test',
            'description' => null,
            'is_default' => false,
            'created_by' => $user->id,
        ]);

        $pipeline->delete();

        $this->assertSoftDeleted('pipelines', [
            'id' => $pipeline->id,
        ]);
    }

    public function test_pipelines_table_foreign_key_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Pipeline::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Invalid Pipeline',
            'description' => null,
            'is_default' => false,
            'created_by' => 'non-existent-user-id',
        ]);
    }

    public function test_pipelines_table_boolean_default_flag(): void
    {
        $user = User::factory()->create();

        $defaultPipeline = Pipeline::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Default Pipeline',
            'description' => null,
            'is_default' => true,
            'created_by' => $user->id,
        ]);

        $nonDefaultPipeline = Pipeline::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Non-Default Pipeline',
            'description' => null,
            'is_default' => false,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('pipelines', [
            'id' => $defaultPipeline->id,
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('pipelines', [
            'id' => $nonDefaultPipeline->id,
            'is_default' => false,
        ]);
    }
}
