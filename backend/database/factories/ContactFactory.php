<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Crm\ContactStatus;
use App\Enums\Crm\LeadLevel;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'lead_level' => fake()->randomElement(LeadLevel::cases()),
            'owner_user_id' => User::factory(),
            'source' => fake()->randomElement(['website', 'referral', 'social_media', 'email_campaign', 'cold_call', 'trade_show', 'partner', 'other']),
            'status' => fake()->randomElement(ContactStatus::cases()),
        ];
    }

    public function lead(): static
    {
        return $this->state(fn (array $attributes) => [
            'lead_level' => LeadLevel::LEAD,
        ]);
    }

    public function contact(): static
    {
        return $this->state(fn (array $attributes) => [
            'lead_level' => LeadLevel::CONTACT,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContactStatus::ACTIVE,
        ]);
    }

    public function newStatus(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContactStatus::NEW,
        ]);
    }
}
