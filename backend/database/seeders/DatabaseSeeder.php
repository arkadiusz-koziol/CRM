<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//         User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'surname' => 'Test',
            'phone' => '123456789',
            'email' => 'admin@example.com',
            'status' => 'active',
        ])->assignRole(UserRoles::ADMIN->value);
    }
}
