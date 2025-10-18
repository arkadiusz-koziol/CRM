<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Car;

use App\Models\Car;
use App\Models\User;
use App\Services\CarService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class CarUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_update_car(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create([
            'name' => 'BMW X5',
            'description' => 'Old description',
            'registration_number' => 'ABC123',
            'technical_details' => 'Old technical details',
        ]);

        $payload = [
            'name' => 'BMW X5 Updated',
            'description' => 'Updated description',
            'registration_number' => 'XYZ789',
            'technical_details' => 'Updated technical details',
        ];

        $response = $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'description',
                    'registration_number',
                    'technical_details',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'name' => 'BMW X5 Updated',
            'description' => 'Updated description',
            'registration_number' => 'XYZ789',
            'technical_details' => 'Updated technical details',
        ]);
    }

    public function test_admin_can_update_car_with_nullable_fields(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create([
            'name' => 'BMW X5',
            'description' => 'Old description',
            'registration_number' => 'ABC123',
            'technical_details' => 'Old technical details',
        ]);

        $payload = [
            'name' => 'BMW X5 Updated',
            'description' => null,
            'registration_number' => 'XYZ789',
            'technical_details' => null,
        ];

        $response = $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertOk();

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'name' => 'BMW X5 Updated',
            'description' => null,
            'registration_number' => 'XYZ789',
            'technical_details' => null,
        ]);
    }

    public function test_validation_error_when_required_fields_missing(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'registration_number']);
    }

    public function test_validation_error_when_name_too_long(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();

        $payload = [
            'name' => str_repeat('a', 256),
            'registration_number' => 'ABC123',
        ];

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_validation_error_when_description_too_long(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();

        $payload = [
            'name' => 'BMW X5',
            'description' => str_repeat('a', 1001),
            'registration_number' => 'ABC123',
        ];

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    }

    public function test_validation_error_when_registration_number_too_long(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();

        $payload = [
            'name' => 'BMW X5',
            'registration_number' => str_repeat('a', 21),
        ];

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['registration_number']);
    }

    public function test_validation_error_when_technical_details_too_long(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();

        $payload = [
            'name' => 'BMW X5',
            'registration_number' => 'ABC123',
            'technical_details' => str_repeat('a', 1001),
        ];

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['technical_details']);
    }

    public function test_returns_404_when_car_not_found(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $payload = [
            'name' => 'BMW X5',
            'registration_number' => 'ABC123',
        ];

        $this->actingAs($admin)
            ->putJson('api/v1/admin/cars/999', $payload)
            ->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_update_car(): void
    {
        $car = Car::factory()->create();

        $payload = [
            'name' => 'BMW X5',
            'registration_number' => 'ABC123',
        ];

        $this->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertStatus(401);
    }

    public function test_user_without_permission_cannot_update_car(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $payload = [
            'name' => 'BMW X5',
            'registration_number' => 'ABC123',
        ];

        $this->actingAs($user)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertForbidden();
    }

    public function test_service_failure_returns_error(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();

        $mockService = Mockery::mock(CarService::class);
        $mockService->shouldReceive('updateCar')
            ->once()
            ->andReturn(false);
        $this->app->instance(CarService::class, $mockService);

        $payload = [
            'name' => 'BMW X5',
            'registration_number' => 'ABC123',
        ];

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertStatus(500)
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }

    public function test_soft_deleted_car_cannot_be_updated(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();
        $car->delete();

        $payload = [
            'name' => 'BMW X5',
            'registration_number' => 'ABC123',
        ];

        $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertStatus(404);
    }

    public function test_registration_number_is_uppercase(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();

        $payload = [
            'name' => 'BMW X5',
            'registration_number' => 'abc123',
        ];

        $response = $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertOk();

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'registration_number' => 'ABC123',
        ]);
    }

    public function test_updated_at_timestamp_is_updated(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.update');

        $car = Car::factory()->create();
        $originalUpdatedAt = $car->updated_at;

        // Advance time by 1 second to ensure timestamps are different
        $this->travel(1)->seconds();

        $payload = [
            'name' => 'BMW X5 Updated',
            'description' => 'Updated description',
            'registration_number' => 'ABC123',
            'technical_details' => 'Updated technical details',
        ];

        $response = $this->actingAs($admin)
            ->putJson("api/v1/admin/cars/{$car->id}", $payload)
            ->assertOk();

        $car->refresh();

        // Check if the car was actually updated
        $this->assertEquals('BMW X5 Updated', $car->name);
        $this->assertEquals('Updated description', $car->description);
        $this->assertEquals('ABC123', $car->registration_number);
        $this->assertEquals('Updated technical details', $car->technical_details);

        // The updated_at should be greater than the original
        $this->assertTrue($car->updated_at->gt($originalUpdatedAt));
    }
}
