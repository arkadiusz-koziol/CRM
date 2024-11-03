<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'surname' => 'Test',
            'phone' => '123456789',
            'email' => 'admin@example.com',
            'status' => 'active',
        ])->assignRole(UserRoles::ADMIN->value);
    }
}
