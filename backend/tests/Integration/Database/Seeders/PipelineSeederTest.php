<?php

declare(strict_types=1);

namespace Tests\Integration\Database\Seeders;

use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;
use Database\Seeders\PipelineSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PipelineSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_seeder_creates_default_pipeline(): void
    {
        $this->seed(PipelineSeeder::class);

        $this->assertDatabaseHas('pipelines', [
            'name' => 'Sales Pipeline',
            'description' => 'Default sales pipeline for CRM opportunities',
            'is_default' => true,
        ]);
    }

    public function test_pipeline_seeder_creates_all_default_stages(): void
    {
        $this->seed(PipelineSeeder::class);

        $pipeline = Pipeline::where('name', 'Sales Pipeline')->first();
        $this->assertNotNull($pipeline);

        $expectedStages = [
            'Prospecting',
            'Demo',
            'Proposal',
            'Negotiation',
            'Won',
            'Lost',
        ];

        foreach ($expectedStages as $stageName) {
            $this->assertDatabaseHas('stages', [
                'pipeline_id' => $pipeline->id,
                'name' => $stageName,
            ]);
        }
    }

    public function test_pipeline_seeder_creates_stages_in_correct_order(): void
    {
        $this->seed(PipelineSeeder::class);

        $pipeline = Pipeline::where('name', 'Sales Pipeline')->first();
        $stages = Stage::where('pipeline_id', $pipeline->id)
            ->orderBy('order')
            ->get();

        $this->assertCount(6, $stages);

        $expectedOrder = [
            ['name' => 'Prospecting', 'order' => 1, 'is_final' => false],
            ['name' => 'Demo', 'order' => 2, 'is_final' => false],
            ['name' => 'Proposal', 'order' => 3, 'is_final' => false],
            ['name' => 'Negotiation', 'order' => 4, 'is_final' => false],
            ['name' => 'Won', 'order' => 5, 'is_final' => true],
            ['name' => 'Lost', 'order' => 6, 'is_final' => true],
        ];

        foreach ($expectedOrder as $index => $expected) {
            $stage = $stages[$index];
            $this->assertEquals($expected['name'], $stage->name);
            $this->assertEquals($expected['order'], $stage->order);
            $this->assertEquals($expected['is_final'], $stage->is_final);
        }
    }

    public function test_pipeline_seeder_creates_user_if_none_exists(): void
    {
        $this->assertDatabaseCount('users', 0);

        $this->seed(PipelineSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('pipelines', [
            'name' => 'Sales Pipeline',
        ]);
    }

    public function test_pipeline_seeder_uses_existing_user(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);

        $this->seed(PipelineSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('pipelines', [
            'name' => 'Sales Pipeline',
            'created_by' => $user->id,
        ]);
    }

    public function test_pipeline_seeder_creates_stages_with_descriptions(): void
    {
        $this->seed(PipelineSeeder::class);

        $pipeline = Pipeline::where('name', 'Sales Pipeline')->first();
        $stages = Stage::where('pipeline_id', $pipeline->id)->get();

        $expectedDescriptions = [
            'Prospecting' => 'Initial contact and qualification',
            'Demo' => 'Product demonstration scheduled',
            'Proposal' => 'Formal proposal submitted',
            'Negotiation' => 'Contract negotiation in progress',
            'Won' => 'Opportunity successfully closed',
            'Lost' => 'Opportunity lost or closed without success',
        ];

        foreach ($stages as $stage) {
            $this->assertEquals(
                $expectedDescriptions[$stage->name],
                $stage->description
            );
        }
    }

    public function test_pipeline_seeder_creates_final_stages_correctly(): void
    {
        $this->seed(PipelineSeeder::class);

        $pipeline = Pipeline::where('name', 'Sales Pipeline')->first();

        $finalStages = Stage::where('pipeline_id', $pipeline->id)
            ->where('is_final', true)
            ->get();

        $this->assertCount(2, $finalStages);

        $finalStageNames = $finalStages->pluck('name')->toArray();
        $this->assertContains('Won', $finalStageNames);
        $this->assertContains('Lost', $finalStageNames);

        $nonFinalStages = Stage::where('pipeline_id', $pipeline->id)
            ->where('is_final', false)
            ->get();

        $this->assertCount(4, $nonFinalStages);
    }
}
