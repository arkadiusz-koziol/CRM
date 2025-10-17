<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Car;

use App\Models\Car;
use App\Models\User;
use App\Services\CarService;
use Database\Seeders\PermissionSeeder;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class CarListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_list_cars_with_pagination(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        $cars = Car::factory()->count(15)->create();

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list?page=1&limit=10')
            ->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'registration_number',
                    'technical_details',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
            'pagination' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
                'from',
                'to',
            ],
        ]);

        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(15, $response->json('pagination.total'));
        $this->assertEquals(2, $response->json('pagination.last_page'));
    }

    public function test_admin_can_list_cars_with_search_by_name(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        Car::factory()->create(['name' => 'BMW X5']);
        Car::factory()->create(['name' => 'Audi A4']);
        Car::factory()->create(['name' => 'Mercedes C-Class']);

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list?search=BMW')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('BMW X5', $response->json('data.0.name'));
    }

    public function test_admin_can_list_cars_with_search_by_registration_number(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        Car::factory()->create(['registration_number' => 'ABC123']);
        Car::factory()->create(['registration_number' => 'XYZ789']);
        Car::factory()->create(['registration_number' => 'DEF456']);

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list?search=ABC')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('ABC123', $response->json('data.0.registration_number'));
    }

    public function test_admin_can_list_cars_with_default_pagination(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        Car::factory()->count(5)->create();

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(10, $response->json('pagination.per_page'));
    }

    public function test_admin_can_list_cars_with_custom_limit(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        Car::factory()->count(25)->create();

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list?limit=20')
            ->assertOk();

        $this->assertCount(20, $response->json('data'));
        $this->assertEquals(20, $response->json('pagination.per_page'));
    }

    public function test_admin_can_list_cars_with_max_limit(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        Car::factory()->count(150)->create();

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list?limit=200')
            ->assertOk();

        $this->assertCount(100, $response->json('data'));
        $this->assertEquals(100, $response->json('pagination.per_page'));
    }

    public function test_admin_can_list_cars_with_min_limit(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        Car::factory()->count(5)->create();

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list?limit=0')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(1, $response->json('pagination.per_page'));
    }

    public function test_admin_can_list_cars_with_invalid_page(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        Car::factory()->count(5)->create();

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list?page=0')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(1, $response->json('pagination.current_page'));
    }

    public function test_admin_can_list_cars_with_empty_search(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        Car::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list?search=')
            ->assertOk();

        $this->assertCount(3, $response->json('data'));
    }

    public function test_admin_can_list_cars_with_no_results(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list')
            ->assertOk();

        $this->assertCount(0, $response->json('data'));
        $this->assertEquals(0, $response->json('pagination.total'));
    }

    public function test_unauthenticated_user_cannot_list_cars(): void
    {
        $this->getJson('api/v1/admin/cars/list')
            ->assertStatus(401);
    }

    public function test_user_without_permission_cannot_list_cars(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('api/v1/admin/cars/list')
            ->assertForbidden();
    }

    public function test_service_failure_returns_error(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        $mockService = Mockery::mock(CarService::class);
        $mockService->shouldReceive('getPaginatedCars')
            ->once()
            ->andThrow(new Exception('Database connection failed'));
        $this->app->instance(CarService::class, $mockService);

        $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list')
            ->assertStatus(500)
            ->assertJsonFragment(['message' => 'Akcja nie powiodła się.']);
    }

    public function test_cars_are_ordered_by_creation_date(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        $car1 = Car::factory()->create(['name' => 'First Car']);
        $car2 = Car::factory()->create(['name' => 'Second Car']);
        $car3 = Car::factory()->create(['name' => 'Third Car']);

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list')
            ->assertOk();

        $cars = $response->json('data');
        $this->assertEquals($car1->id, $cars[0]['id']);
        $this->assertEquals($car2->id, $cars[1]['id']);
        $this->assertEquals($car3->id, $cars[2]['id']);
    }

    public function test_soft_deleted_cars_are_not_included(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('car.list');

        $car1 = Car::factory()->create(['name' => 'Active Car']);
        $car2 = Car::factory()->create(['name' => 'Deleted Car']);
        $car2->delete();

        $response = $this->actingAs($admin)
            ->getJson('api/v1/admin/cars/list')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Active Car', $response->json('data.0.name'));
    }
}
