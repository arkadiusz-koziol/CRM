<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Nail',
                'description' => 'Big head, made from steel, wood-use.',
                'count' => 1000000,
                'price' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Screw',
                'description' => 'Steel, for metal and wood use.',
                'count' => 500000,
                'price' => 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bolt',
                'description' => 'Strong, for heavy machinery.',
                'count' => 300000,
                'price' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Washer',
                'description' => 'Prevents damage to surfaces.',
                'count' => 800000,
                'price' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nut',
                'description' => 'Secures bolts and screws.',
                'count' => 600000,
                'price' => 7000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plywood',
                'description' => 'Layered wood sheets.',
                'count' => 10000,
                'price' => 500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paint',
                'description' => 'Water-based, for walls.',
                'count' => 20000,
                'price' => 30000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Glue',
                'description' => 'Strong adhesive, multi-purpose.',
                'count' => 50000,
                'price' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sandpaper',
                'description' => 'Various grits, for smoothing surfaces.',
                'count' => 100000,
                'price' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wire',
                'description' => 'Copper, insulated, electrical use.',
                'count' => 250000,
                'price' => 12000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($materials as $material) {
            DB::table('materials')->updateOrInsert(
                ['name' => $material['name']],
                [
                    'description' => $material['description'],
                    'count' => $material['count'],
                    'price' => $material['price'],
                ]
            );
        }
    }
}
