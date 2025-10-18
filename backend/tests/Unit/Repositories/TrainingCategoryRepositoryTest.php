<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Domain\TrainingCategory\Entity\TrainingCategory as TrainingCategoryEntity;
use App\Factory\TrainingCategoryDtoFactory;
use App\Models\TrainingCategory;
use App\Repositories\TrainingCategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TrainingCategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TrainingCategoryRepository $trainingCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trainingCategoryRepository = new TrainingCategoryRepository(new TrainingCategory);
    }

    public function test_create_training_category_creates_record(): void
    {
        $trainingCategoryDto = TrainingCategoryDtoFactory::fromArray([
            'name' => 'Safety Training',
        ]);

        $result = $this->trainingCategoryRepository->createTrainingCategory($trainingCategoryDto);

        $this->assertInstanceOf(TrainingCategoryEntity::class, $result);
        $this->assertEquals('Safety Training', $result->name());
        $this->assertDatabaseHas('trainings_categories', [
            'name' => 'Safety Training',
        ]);
    }

    public function test_find_by_id_returns_entity(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $result = $this->trainingCategoryRepository->findTrainingCategoryById($trainingCategory->id);

        $this->assertInstanceOf(TrainingCategoryEntity::class, $result);
        $this->assertEquals($trainingCategory->id, $result->id());
        $this->assertEquals('Safety Training', $result->name());
    }

    public function test_find_by_id_returns_null_if_not_found(): void
    {
        $result = $this->trainingCategoryRepository->findTrainingCategoryById('550e8400-e29b-41d4-a716-446655440000');

        $this->assertNull($result);
    }

    public function test_find_all_returns_collection_of_entities(): void
    {
        TrainingCategory::factory()->count(3)->create();

        $result = $this->trainingCategoryRepository->findAll();

        $this->assertCount(3, $result);
        $this->assertInstanceOf(TrainingCategoryEntity::class, $result->first());
    }

    public function test_update_training_category_training_category_update_training_categorys_record(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);
        $trainingCategoryEntity = $this->trainingCategoryRepository->findTrainingCategoryById($trainingCategory->id);
        $updateTrainingCategorydDto = TrainingCategoryDtoFactory::fromArray([
            'name' => 'Updated Safety Training',
        ]);

        $result = $this->trainingCategoryRepository->updateTrainingCategory($trainingCategoryEntity, $updateTrainingCategorydDto);

        $this->assertInstanceOf(TrainingCategoryEntity::class, $result);
        $this->assertEquals('Updated Safety Training', $result->name());
        $this->assertDatabaseHas('trainings_categories', [
            'id' => $trainingCategory->id,
            'name' => 'Updated Safety Training',
        ]);
    }

    public function test_update_training_category_throws_exception_if_not_found(): void
    {
        $trainingCategoryEntity = new TrainingCategoryEntity(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Safety Training',
            createdAt: now(),
            updatedAt: now()
        );
        $updateTrainingCategorydDto = TrainingCategoryDtoFactory::fromArray([
            'name' => 'Updated Safety Training',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Training category not found');

        $this->trainingCategoryRepository->updateTrainingCategory($trainingCategoryEntity, $updateTrainingCategorydDto);
    }

    public function test_delete_training_category_training_category_delete_training_categorys_record(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);
        $trainingCategoryEntity = $this->trainingCategoryRepository->findTrainingCategoryById($trainingCategory->id);

        $result = $this->trainingCategoryRepository->deleteTrainingCategory($trainingCategoryEntity);

        $this->assertTrue($result);
        $this->assertSoftDeleted('trainings_categories', [
            'id' => $trainingCategory->id,
        ]);
    }

    public function test_delete_training_category_returns_false_if_not_found(): void
    {
        $trainingCategoryEntity = new TrainingCategoryEntity(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Safety Training',
            createdAt: now(),
            updatedAt: now()
        );

        $result = $this->trainingCategoryRepository->deleteTrainingCategory($trainingCategoryEntity);

        $this->assertFalse($result);
    }
}
