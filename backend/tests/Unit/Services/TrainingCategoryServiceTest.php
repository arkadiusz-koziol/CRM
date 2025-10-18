<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Domain\TrainingCategory\Entity\TrainingCategory as TrainingCategoryEntity;
use App\Interfaces\Repositories\TrainingCategoryRepositoryInterface;
use App\Services\TrainingCategoryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class TrainingCategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingCategoryService $trainingCategoryService;

    private $trainingCategoryRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trainingCategoryRepositoryMock = Mockery::mock(TrainingCategoryRepositoryInterface::class);

        $this->trainingCategoryService = new TrainingCategoryService(
            $this->trainingCategoryRepositoryMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_category_calls_repository(): void
    {
        $data = ['name' => 'Safety Training'];
        $trainingCategoryEntity = new TrainingCategoryEntity(
            id: 'test-uuid',
            name: 'Safety Training',
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($dto) {
                return $dto->name() === 'Safety Training';
            }))
            ->andReturn($trainingCategoryEntity);

        $result = $this->trainingCategoryService->createCategory($data);

        $this->assertSame($trainingCategoryEntity, $result);
    }

    public function test_get_category_calls_repository(): void
    {
        $id = 'test-uuid';
        $trainingCategoryEntity = new TrainingCategoryEntity(
            id: $id,
            name: 'Safety Training',
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($trainingCategoryEntity);

        $result = $this->trainingCategoryService->getCategory($id);

        $this->assertSame($trainingCategoryEntity, $result);
    }

    public function test_get_all_categories_calls_repository(): void
    {
        $categories = collect([
            new TrainingCategoryEntity(
                id: 'uuid-1',
                name: 'Safety Training',
                createdAt: Carbon::now(),
                updatedAt: Carbon::now()
            ),
            new TrainingCategoryEntity(
                id: 'uuid-2',
                name: 'Technical Training',
                createdAt: Carbon::now(),
                updatedAt: Carbon::now()
            ),
        ]);

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('findAll')
            ->once()
            ->andReturn($categories);

        $result = $this->trainingCategoryService->getAllCategories();

        $this->assertSame($categories, $result);
    }

    public function test_update_category_calls_repository(): void
    {
        $id = 'test-uuid';
        $data = ['name' => 'Updated Safety Training'];
        $existingCategory = new TrainingCategoryEntity(
            id: $id,
            name: 'Safety Training',
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );
        $updatedCategory = new TrainingCategoryEntity(
            id: $id,
            name: 'Updated Safety Training',
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($existingCategory);

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($existingCategory, Mockery::on(function ($dto) {
                return $dto->name() === 'Updated Safety Training';
            }))
            ->andReturn($updatedCategory);

        $result = $this->trainingCategoryService->updateCategory($id, $data);

        $this->assertSame($updatedCategory, $result);
    }

    public function test_update_category_throws_exception_if_not_found(): void
    {
        $id = 'non-existent-uuid';
        $data = ['name' => 'Updated Safety Training'];

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Training category not found');

        $this->trainingCategoryService->updateCategory($id, $data);
    }

    public function test_delete_category_calls_repository(): void
    {
        $id = 'test-uuid';
        $trainingCategoryEntity = new TrainingCategoryEntity(
            id: $id,
            name: 'Safety Training',
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($trainingCategoryEntity);

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($trainingCategoryEntity)
            ->andReturn(true);

        $result = $this->trainingCategoryService->deleteCategory($id);

        $this->assertTrue($result);
    }

    public function test_delete_category_throws_exception_if_not_found(): void
    {
        $id = 'non-existent-uuid';

        $this->trainingCategoryRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Training category not found');

        $this->trainingCategoryService->deleteCategory($id);
    }
}
