<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Reports\ReportRunStatus;
use App\Models\Report;
use App\Models\ReportRun;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ReportRunFactory extends Factory
{
    protected $model = ReportRun::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'report_id' => Report::factory(),
            'run_by' => User::factory(),
            'status' => $this->faker->randomElement(ReportRunStatus::cases()),
            'parameters' => $this->faker->optional(0.3)->randomElements([
                ['date_range' => 'last_30_days'],
                ['status_filter' => 'active'],
            ], 1),
            'file_path' => $this->faker->optional(0.7)->filePath(),
            'file_name' => $this->faker->optional(0.7)->word().'.csv',
            'file_size' => $this->faker->optional(0.7)->numberBetween(1024, 1048576),
            'mime_type' => $this->faker->optional(0.7)->randomElement(['text/csv', 'application/csv']),
            'started_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 hour', 'now'),
            'completed_at' => $this->faker->optional(0.6)->dateTimeBetween('-1 hour', 'now'),
            'error_message' => $this->faker->optional(0.1)->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportRunStatus::PENDING,
            'started_at' => null,
            'completed_at' => null,
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
            'error_message' => null,
        ]);
    }

    public function running(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportRunStatus::RUNNING,
            'started_at' => now()->subMinutes(5),
            'completed_at' => null,
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
            'error_message' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportRunStatus::COMPLETED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'file_path' => 'reports/'.\Illuminate\Support\Str::uuid().'.csv',
            'file_name' => 'report_'.\Illuminate\Support\Str::uuid().'.csv',
            'file_size' => $this->faker->numberBetween(1024, 1048576),
            'mime_type' => 'text/csv',
            'error_message' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportRunStatus::FAILED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
            'error_message' => 'Report generation failed due to database connection error.',
        ]);
    }
}
