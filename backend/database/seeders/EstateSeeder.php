<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstateSeeder extends Seeder
{
    public function run(): void
    {
        $estates = [
            [
                'name' => 'Biedronka',
                'custom_id' => Str::random(8),
                'street' => 'Gubaszewska',
                'postal_code' => '59-700',
                'city' => 'Wrocław',
                'house_number' => '12a/3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lidl',
                'custom_id' => Str::random(8),
                'street' => 'Krakowska',
                'postal_code' => '30-001',
                'city' => 'Kraków',
                'house_number' => '15b',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carrefour',
                'custom_id' => Str::random(8),
                'street' => 'Warszawska',
                'postal_code' => '00-001',
                'city' => 'Warszawa',
                'house_number' => '7/4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Auchan',
                'custom_id' => Str::random(8),
                'street' => 'Poznańska',
                'postal_code' => '61-001',
                'city' => 'Poznań',
                'house_number' => '22',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tesco',
                'custom_id' => Str::random(8),
                'street' => 'Gdańska',
                'postal_code' => '80-001',
                'city' => 'Gdańsk',
                'house_number' => '5/3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($estates as $estate) {
            $city = DB::table('cities')->where('name', $estate['city'])->first();

            if ($city) {
                DB::table('estates')->updateOrInsert(
                    [
                        'custom_id' => $estate['custom_id'],
                    ],
                    [
                        'name' => $estate['name'],
                        'street' => $estate['street'],
                        'postal_code' => $estate['postal_code'],
                        'city_id' => $city->id,
                        'house_number' => $estate['house_number'],
                    ]
                );
            }
        }
    }
}
