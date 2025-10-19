<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Reports\ReportSource;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $source = $this->faker->randomElement(ReportSource::cases());
        $allowedColumns = $source->getAllowedColumns();

        return [
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => $this->faker->words(3, true).' Report',
            'description' => $this->faker->optional(0.7)->sentence(),
            'source' => $source,
            'columns' => $this->faker->randomElements($allowedColumns, $this->faker->numberBetween(2, 4)),
            'filters' => $this->faker->optional(0.5)->randomElements([
                [['field' => 'status', 'operator' => 'eq', 'value' => 'active']],
                [['field' => 'created_at', 'operator' => 'gte', 'value' => now()->subDays(30)->toDateString()]],
            ], 1),
            'sorting' => $this->faker->optional(0.6)->randomElements([
                [['field' => 'name', 'direction' => 'asc']],
                [['field' => 'created_at', 'direction' => 'desc']],
            ], 1),
            'created_by' => User::factory(),
            'is_public' => $this->faker->boolean(30),
        ];
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }
}
