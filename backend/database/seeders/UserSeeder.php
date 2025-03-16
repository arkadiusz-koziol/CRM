<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Enums\UserStatus;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole(UserRoles::USER->value);
        });

        if (!User::where('email', 'admin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'status' => UserStatus::ACTIVE->value,
            ])->assignRole(UserRoles::ADMIN->value);
        }
    }
}
