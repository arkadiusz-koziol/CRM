<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create sample tasks
        Task::factory(50)
            ->sequence(
                ['status' => 'pending'],
                ['status' => 'in_progress'],
                ['status' => 'completed'],
                ['status' => 'on_hold'],
                ['status' => 'cancelled'],
            )
            ->sequence(
                ['priority' => 'low'],
                ['priority' => 'medium'],
                ['priority' => 'high'],
                ['priority' => 'urgent'],
            )
            ->create([
                'assigned_to' => fn() => $users->random()->id,
                'created_by' => fn() => $users->random()->id,
            ]);

        $this->command->info('Tasks seeded successfully!');
    }
}
