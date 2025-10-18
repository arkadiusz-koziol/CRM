<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class PipelineSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have at least one user
        $user = \App\Models\User::first();
        if (! $user) {
            $user = \App\Models\User::factory()->create();
        }

        // Create default sales pipeline
        $pipeline = Pipeline::create([
            'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            'name' => 'Sales Pipeline',
            'description' => 'Default sales pipeline for CRM opportunities',
            'is_default' => true,
            'created_by' => $user->id,
        ]);

        // Create default stages in order
        $stages = [
            [
                'name' => 'Prospecting',
                'description' => 'Initial contact and qualification',
                'order' => 1,
                'is_final' => false,
            ],
            [
                'name' => 'Demo',
                'description' => 'Product demonstration scheduled',
                'order' => 2,
                'is_final' => false,
            ],
            [
                'name' => 'Proposal',
                'description' => 'Formal proposal submitted',
                'order' => 3,
                'is_final' => false,
            ],
            [
                'name' => 'Negotiation',
                'description' => 'Contract negotiation in progress',
                'order' => 4,
                'is_final' => false,
            ],
            [
                'name' => 'Won',
                'description' => 'Opportunity successfully closed',
                'order' => 5,
                'is_final' => true,
            ],
            [
                'name' => 'Lost',
                'description' => 'Opportunity lost or closed without success',
                'order' => 6,
                'is_final' => true,
            ],
        ];

        foreach ($stages as $stageData) {
            Stage::create([
                'id' => \Ramsey\Uuid\Uuid::uuid7()->toString(),
                'pipeline_id' => $pipeline->id,
                ...$stageData,
            ]);
        }
    }
}
