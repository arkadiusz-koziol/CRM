<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Dto\CarDto;
use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CarRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CarRepository $carRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carRepository = new CarRepository(new Car);
    }

    public function test_create_car_saves_to_database(): void
    {
        $carDto = new CarDto(
            name: 'BMW X5',
            description: 'Luxury SUV',
            registrationNumber: 'ABC123',
            technicalDetails: 'V8 Engine'
        );

        $car = $this->carRepository->createCar($carDto);

        $this->assertInstanceOf(Car::class, $car);
        $this->assertEquals('BMW X5', $car->name);
        $this->assertEquals('Luxury SUV', $car->description);
        $this->assertEquals('ABC123', $car->registration_number);
        $this->assertEquals('V8 Engine', $car->technical_details);

        $this->assertDatabaseHas('cars', [
            'name' => 'BMW X5',
            'description' => 'Luxury SUV',
            'registration_number' => 'ABC123',
            'technical_details' => 'V8 Engine',
        ]);
    }

    public function test_find_all_returns_all_cars(): void
    {
        Car::factory()->count(3)->create();

        $cars = $this->carRepository->findAllCars();

        $this->assertCount(3, $cars);
        $this->assertIsArray($cars);
    }

    public function test_find_all_returns_empty_array_when_no_cars(): void
    {
        $cars = $this->carRepository->findAllCars();

        $this->assertCount(0, $cars);
        $this->assertIsArray($cars);
    }

    public function test_find_paginated_returns_correct_structure(): void
    {
        Car::factory()->count(25)->create();

        $result = $this->carRepository->findPaginated(1, 10);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(10, $result['data']);
        $this->assertEquals(25, $result['pagination']['total']);
        $this->assertEquals(3, $result['pagination']['last_page']);
    }

    public function test_find_paginated_with_search_filters_by_name(): void
    {
        Car::factory()->create(['name' => 'BMW X5']);
        Car::factory()->create(['name' => 'Audi A4']);
        Car::factory()->create(['name' => 'BMW M3']);

        $result = $this->carRepository->findPaginated(1, 10, 'BMW');

        $this->assertCount(2, $result['data']);
        $this->assertEquals(2, $result['pagination']['total']);
    }

    public function test_find_paginated_with_search_filters_by_registration_number(): void
    {
        Car::factory()->create(['registration_number' => 'ABC123']);
        Car::factory()->create(['registration_number' => 'XYZ789']);
        Car::factory()->create(['registration_number' => 'ABC456']);

        $result = $this->carRepository->findPaginated(1, 10, 'ABC');

        $this->assertCount(2, $result['data']);
        $this->assertEquals(2, $result['pagination']['total']);
    }

    public function test_find_paginated_with_empty_search_returns_all(): void
    {
        Car::factory()->count(5)->create();

        $result = $this->carRepository->findPaginated(1, 10, '');

        $this->assertCount(5, $result['data']);
        $this->assertEquals(5, $result['pagination']['total']);
    }

    public function test_find_paginated_handles_invalid_page(): void
    {
        Car::factory()->count(5)->create();

        $result = $this->carRepository->findPaginated(0, 10);

        $this->assertCount(5, $result['data']);
        $this->assertEquals(1, $result['pagination']['current_page']);
    }

    public function test_find_paginated_handles_large_page_number(): void
    {
        Car::factory()->count(5)->create();

        $result = $this->carRepository->findPaginated(999, 10);

        $this->assertCount(0, $result['data']);
        $this->assertEquals(999, $result['pagination']['current_page']);
        $this->assertEquals(0, $result['pagination']['from']);
        $this->assertEquals(0, $result['pagination']['to']);
    }

    public function test_find_by_id_returns_car_when_exists(): void
    {
        $car = Car::factory()->create(['name' => 'BMW X5']);

        $result = $this->carRepository->findById($car->id);

        $this->assertInstanceOf(Car::class, $result);
        $this->assertEquals($car->id, $result->id);
        $this->assertEquals('BMW X5', $result->name);
    }

    public function test_find_by_id_returns_null_when_not_exists(): void
    {
        $result = $this->carRepository->findById(999);

        $this->assertNull($result);
    }

    public function test_soft_deleted_cars_are_excluded_from_queries(): void
    {
        $car1 = Car::factory()->create(['name' => 'Active Car']);
        $car2 = Car::factory()->create(['name' => 'Deleted Car']);
        $car2->delete();

        $allCars = $this->carRepository->findAllCars();
        $this->assertCount(1, $allCars);

        $paginatedCars = $this->carRepository->findPaginated(1, 10);
        $this->assertCount(1, $paginatedCars['data']);

        $foundCar = $this->carRepository->findById($car1->id);
        $this->assertInstanceOf(Car::class, $foundCar);

        $deletedCar = $this->carRepository->findById($car2->id);
        $this->assertNull($deletedCar);
    }
}
