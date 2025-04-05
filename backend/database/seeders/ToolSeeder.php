<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            [
                'name' => 'Hammer',
                'description' => 'Black, small, wood-made.',
                'count' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Screwdriver',
                'description' => 'Flat-head, ergonomic handle.',
                'count' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wrench',
                'description' => 'Adjustable, for various bolt sizes.',
                'count' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pliers',
                'description' => 'Needle-nose, for precision work.',
                'count' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Saw',
                'description' => 'Hand saw, for cutting wood.',
                'count' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Drill',
                'description' => 'Cordless, with multiple drill bits.',
                'count' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chisel',
                'description' => 'Woodworking chisel, sharp blade.',
                'count' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tape Measure',
                'description' => 'Retractable, 10 meters long.',
                'count' => 400,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Level',
                'description' => 'Spirit level, for ensuring surfaces are even.',
                'count' => 75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sledgehammer',
                'description' => 'Heavy, for demolition work.',
                'count' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tools as $tool) {
            DB::table('tools')->updateOrInsert(
                ['name' => $tool['name']],
                [
                    'description' => $tool['description'],
                    'count' => $tool['count'],
                ]
            );
        }
    }
}
