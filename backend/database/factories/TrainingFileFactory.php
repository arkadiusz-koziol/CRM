<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainingFile>
 */
final class TrainingFileFactory extends Factory
{
    public function definition(): array
    {
        $fileName = Str::uuid().'.pdf';

        return [
            'training_id' => Training::factory(),
            'original_name' => $this->faker->word().'.pdf',
            'file_name' => $fileName,
            'file_path' => 'training-files/'.$fileName,
            'mime_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(1000, 10000),
        ];
    }
}
