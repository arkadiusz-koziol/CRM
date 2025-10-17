<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Dto\CarDto;
use App\Interfaces\Repositories\CarRepositoryInterface;
use App\Models\Car;
use App\Services\CarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class CarServiceTest extends TestCase
{
    use RefreshDatabase;

    private CarService $carService;

    private CarRepositoryInterface $carRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carRepository = Mockery::mock(CarRepositoryInterface::class);
        $this->carService = new CarService($this->carRepository);
    }

    public function test_create_car_calls_repository(): void
    {
        $carDto = new CarDto(
            name: 'BMW X5',
            description: 'Luxury SUV',
            registrationNumber: 'ABC123',
            technicalDetails: 'V8 Engine'
        );

        $expectedCar = new Car;
        $expectedCar->id = 1;
        $expectedCar->name = 'BMW X5';
        $expectedCar->description = 'Luxury SUV';
        $expectedCar->registration_number = 'ABC123';
        $expectedCar->technical_details = 'V8 Engine';

        $this->carRepository
            ->shouldReceive('create')
            ->once()
            ->with($carDto)
            ->andReturn($expectedCar);

        $result = $this->carService->createCar($carDto);

        $this->assertInstanceOf(Car::class, $result);
        $this->assertEquals('BMW X5', $result->name);
    }

    public function test_get_all_cars_calls_repository(): void
    {
        $expectedCars = [
            ['id' => 1, 'name' => 'BMW X5'],
            ['id' => 2, 'name' => 'Audi A4'],
        ];

        $this->carRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn($expectedCars);

        $result = $this->carService->getAllCars();

        $this->assertEquals($expectedCars, $result);
    }

    public function test_get_paginated_cars_calls_repository_with_parameters(): void
    {
        $expectedResult = [
            'data' => [
                ['id' => 1, 'name' => 'BMW X5'],
                ['id' => 2, 'name' => 'Audi A4'],
            ],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 10,
                'total' => 2,
                'last_page' => 1,
                'from' => 1,
                'to' => 2,
            ],
        ];

        $this->carRepository
            ->shouldReceive('findPaginated')
            ->once()
            ->with(1, 10, 'BMW')
            ->andReturn($expectedResult);

        $result = $this->carService->getPaginatedCars(1, 10, 'BMW');

        $this->assertEquals($expectedResult, $result);
    }

    public function test_get_paginated_cars_with_default_parameters(): void
    {
        $expectedResult = [
            'data' => [],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 10,
                'total' => 0,
                'last_page' => 0,
                'from' => 0,
                'to' => 0,
            ],
        ];

        $this->carRepository
            ->shouldReceive('findPaginated')
            ->once()
            ->with(1, 10, '')
            ->andReturn($expectedResult);

        $result = $this->carService->getPaginatedCars();

        $this->assertEquals($expectedResult, $result);
    }

    public function test_get_car_by_id_calls_repository(): void
    {
        $expectedCar = new Car;
        $expectedCar->id = 1;
        $expectedCar->name = 'BMW X5';

        $this->carRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($expectedCar);

        $result = $this->carService->getCarById(1);

        $this->assertInstanceOf(Car::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_get_car_by_id_returns_null_when_not_found(): void
    {
        $this->carRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->carService->getCarById(999);

        $this->assertNull($result);
    }

    public function test_update_car_calls_repository(): void
    {
        $car = new Car;
        $car->id = 1;
        $car->name = 'BMW X5';

        $carDto = new CarDto(
            name: 'BMW X5 Updated',
            description: 'Updated description',
            registrationNumber: 'XYZ789',
            technicalDetails: 'Updated technical details'
        );

        $this->carRepository
            ->shouldReceive('updateCar')
            ->once()
            ->with($car, $carDto)
            ->andReturn(true);

        $result = $this->carService->updateCar($car, $carDto);

        $this->assertTrue($result);
    }

    public function test_update_car_returns_false_when_repository_fails(): void
    {
        $car = new Car;
        $car->id = 1;

        $carDto = new CarDto(
            name: 'BMW X5 Updated',
            description: 'Updated description',
            registrationNumber: 'XYZ789',
            technicalDetails: 'Updated technical details'
        );

        $this->carRepository
            ->shouldReceive('updateCar')
            ->once()
            ->with($car, $carDto)
            ->andReturn(false);

        $result = $this->carService->updateCar($car, $carDto);

        $this->assertFalse($result);
    }

    public function test_delete_car_calls_repository(): void
    {
        $car = new Car;
        $car->id = 1;
        $car->name = 'BMW X5';

        $this->carRepository
            ->shouldReceive('deleteCar')
            ->once()
            ->with($car)
            ->andReturn(true);

        $result = $this->carService->deleteCar($car);

        $this->assertTrue($result);
    }

    public function test_delete_car_returns_false_when_repository_fails(): void
    {
        $car = new Car;
        $car->id = 1;

        $this->carRepository
            ->shouldReceive('deleteCar')
            ->once()
            ->with($car)
            ->andReturn(false);

        $result = $this->carService->deleteCar($car);

        $this->assertFalse($result);
    }
}
