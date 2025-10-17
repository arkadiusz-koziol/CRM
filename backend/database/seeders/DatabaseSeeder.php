<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            MaterialSeeder::class,
            ToolSeeder::class,
            CitySeeder::class,
            EstateSeeder::class,
            CarSeeder::class,
            TaskSeeder::class,
        ]);

    }
}
