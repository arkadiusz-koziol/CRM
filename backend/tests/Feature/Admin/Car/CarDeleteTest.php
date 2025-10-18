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

final class CarDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_delete_car(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car = Car::factory()->create([
            'name' => 'BMW X5',
            'description' => 'Luxury SUV',
            'registration_number' => 'ABC123',
            'technical_details' => 'V8 Engine',
        ]);

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'name' => 'BMW X5',
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(204);

        $this->assertSoftDeleted('cars', [
            'id' => $car->id,
        ]);
    }

    public function test_returns_404_when_car_not_found(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $this->actingAs($admin, 'web')
            ->deleteJson('api/v1/admin/cars/999')
            ->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_delete_car(): void
    {
        $car = Car::factory()->create();

        $this->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(401);
    }

    public function test_user_without_permission_cannot_delete_car(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $this->actingAs($user)
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertForbidden();
    }

    public function test_service_failure_returns_error(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car = Car::factory()->create();

        $mockService = Mockery::mock(CarService::class);
        $mockService->shouldReceive('deleteCar')
            ->once()
            ->andReturn(false);
        $this->app->instance(CarService::class, $mockService);

        $this->actingAs($admin, 'web')
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(500)
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }

    public function test_already_deleted_car_returns_404(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car = Car::factory()->create();
        $car->delete();

        $this->actingAs($admin, 'web')
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(404);
    }

    public function test_deleted_car_is_soft_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car = Car::factory()->create();

        $this->actingAs($admin, 'web')
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(204);

        $this->assertSoftDeleted('cars', [
            'id' => $car->id,
        ]);

        $this->assertDatabaseMissing('cars', [
            'id' => $car->id,
            'deleted_at' => null,
        ]);
    }

    public function test_deleted_car_can_be_restored(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car = Car::factory()->create();

        $this->actingAs($admin, 'web')
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(204);

        $this->assertSoftDeleted('cars', [
            'id' => $car->id,
        ]);

        $car->restore();

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'deleted_at' => null,
        ]);
    }

    public function test_deleted_car_has_timestamp(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car = Car::factory()->create();

        $this->actingAs($admin, 'web')
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(204);

        $car->refresh();

        $this->assertNotNull($car->deleted_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $car->deleted_at);
    }

    public function test_multiple_cars_can_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car1 = Car::factory()->create(['name' => 'BMW X5']);
        $car2 = Car::factory()->create(['name' => 'Audi A4']);
        $car3 = Car::factory()->create(['name' => 'Mercedes C-Class']);

        $this->actingAs($admin, 'web')
            ->deleteJson("api/v1/admin/cars/{$car1->id}")
            ->assertStatus(204);

        $this->actingAs($admin, 'web')
            ->deleteJson("api/v1/admin/cars/{$car2->id}")
            ->assertStatus(204);

        $this->assertSoftDeleted('cars', ['id' => $car1->id]);
        $this->assertSoftDeleted('cars', ['id' => $car2->id]);
        $this->assertDatabaseHas('cars', [
            'id' => $car3->id,
            'deleted_at' => null,
        ]);
    }

    public function test_delete_operation_is_logged(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car = Car::factory()->create();

        $this->actingAs($admin, 'web')
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(204);

        $this->assertSoftDeleted('cars', ['id' => $car->id]);
    }

    public function test_delete_returns_no_content_response(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.delete');

        $car = Car::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("api/v1/admin/cars/{$car->id}")
            ->assertStatus(204);

        $this->assertEmpty($response->getContent());
    }
}
