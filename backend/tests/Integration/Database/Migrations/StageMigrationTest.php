<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Migrations;

use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_stages_table_has_correct_structure(): void
    {
        $this->assertTrue(\Schema::hasTable('stages'));
        $this->assertTrue(\Schema::hasColumn('stages', 'id'));
        $this->assertTrue(\Schema::hasColumn('stages', 'pipeline_id'));
        $this->assertTrue(\Schema::hasColumn('stages', 'name'));
        $this->assertTrue(\Schema::hasColumn('stages', 'description'));
        $this->assertTrue(\Schema::hasColumn('stages', 'order'));
        $this->assertTrue(\Schema::hasColumn('stages', 'is_final'));
        $this->assertTrue(\Schema::hasColumn('stages', 'created_at'));
        $this->assertTrue(\Schema::hasColumn('stages', 'updated_at'));
        $this->assertTrue(\Schema::hasColumn('stages', 'deleted_at'));
    }

    public function test_stages_table_accepts_valid_data(): void
    {
        $user = User::factory()->create();
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);

        $stage = Stage::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'pipeline_id' => $pipeline->id,
            'name' => 'Prospecting',
            'description' => 'Initial contact and qualification',
            'order' => 1,
            'is_final' => false,
        ]);

        $this->assertDatabaseHas('stages', [
            'pipeline_id' => $pipeline->id,
            'name' => 'Prospecting',
            'description' => 'Initial contact and qualification',
            'order' => 1,
            'is_final' => false,
        ]);
    }

    public function test_stages_table_accepts_nullable_description(): void
    {
        $user = User::factory()->create();
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);

        $stage = Stage::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'pipeline_id' => $pipeline->id,
            'name' => 'Demo',
            'description' => null,
            'order' => 2,
            'is_final' => false,
        ]);

        $this->assertDatabaseHas('stages', [
            'pipeline_id' => $pipeline->id,
            'name' => 'Demo',
            'description' => null,
            'order' => 2,
            'is_final' => false,
        ]);
    }

    public function test_stages_table_soft_deletes(): void
    {
        $user = User::factory()->create();
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);

        $stage = Stage::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'pipeline_id' => $pipeline->id,
            'name' => 'Soft Delete Test',
            'description' => null,
            'order' => 1,
            'is_final' => false,
        ]);

        $stage->delete();

        $this->assertSoftDeleted('stages', [
            'id' => $stage->id,
        ]);
    }

    public function test_stages_table_foreign_key_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Stage::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'pipeline_id' => 'non-existent-pipeline-id',
            'name' => 'Invalid Stage',
            'description' => null,
            'order' => 1,
            'is_final' => false,
        ]);
    }

    public function test_stages_table_boolean_final_flag(): void
    {
        $user = User::factory()->create();
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);

        $finalStage = Stage::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'pipeline_id' => $pipeline->id,
            'name' => 'Won',
            'description' => 'Opportunity successfully closed',
            'order' => 5,
            'is_final' => true,
        ]);

        $nonFinalStage = Stage::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'pipeline_id' => $pipeline->id,
            'name' => 'Prospecting',
            'description' => 'Initial contact',
            'order' => 1,
            'is_final' => false,
        ]);

        $this->assertDatabaseHas('stages', [
            'id' => $finalStage->id,
            'is_final' => true,
        ]);

        $this->assertDatabaseHas('stages', [
            'id' => $nonFinalStage->id,
            'is_final' => false,
        ]);
    }

    public function test_stages_table_handles_order_values(): void
    {
        $user = User::factory()->create();
        $pipeline = Pipeline::factory()->create(['created_by' => $user->id]);

        $stages = [];
        for ($i = 1; $i <= 5; $i++) {
            $stages[] = Stage::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'pipeline_id' => $pipeline->id,
                'name' => "Stage {$i}",
                'description' => null,
                'order' => $i,
                'is_final' => false,
            ]);
        }

        foreach ($stages as $index => $stage) {
            $this->assertDatabaseHas('stages', [
                'id' => $stage->id,
                'order' => $index + 1,
            ]);
        }
    }
}
