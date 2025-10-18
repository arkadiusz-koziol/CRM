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

        $result = $this->trainingCategoryRepository->create($trainingCategoryDto);

        $this->assertInstanceOf(TrainingCategoryEntity::class, $result);
        $this->assertEquals('Safety Training', $result->name());
        $this->assertDatabaseHas('trainings_categories', [
            'name' => 'Safety Training',
        ]);
    }

    public function test_find_by_id_returns_entity(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);

        $result = $this->trainingCategoryRepository->findById($trainingCategory->id);

        $this->assertInstanceOf(TrainingCategoryEntity::class, $result);
        $this->assertEquals($trainingCategory->id, $result->id());
        $this->assertEquals('Safety Training', $result->name());
    }

    public function test_find_by_id_returns_null_if_not_found(): void
    {
        $result = $this->trainingCategoryRepository->findById('non-existent-uuid');

        $this->assertNull($result);
    }

    public function test_find_all_returns_collection_of_entities(): void
    {
        TrainingCategory::factory()->count(3)->create();

        $result = $this->trainingCategoryRepository->findAll();

        $this->assertCount(3, $result);
        $this->assertInstanceOf(TrainingCategoryEntity::class, $result->first());
    }

    public function test_update_training_category_updates_record(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);
        $trainingCategoryEntity = $this->trainingCategoryRepository->findById($trainingCategory->id);
        $updatedDto = TrainingCategoryDtoFactory::fromArray([
            'name' => 'Updated Safety Training',
        ]);

        $result = $this->trainingCategoryRepository->update($trainingCategoryEntity, $updatedDto);

        $this->assertInstanceOf(TrainingCategoryEntity::class, $result);
        $this->assertEquals('Updated Safety Training', $result->name());
        $this->assertDatabaseHas('trainings_categories', [
            'id' => $trainingCategory->id,
            'name' => 'Updated Safety Training',
        ]);
    }

    public function test_update_throws_exception_if_not_found(): void
    {
        $trainingCategoryEntity = new TrainingCategoryEntity(
            id: 'non-existent-uuid',
            name: 'Safety Training',
            createdAt: now(),
            updatedAt: now()
        );
        $updatedDto = TrainingCategoryDtoFactory::fromArray([
            'name' => 'Updated Safety Training',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Training category not found');

        $this->trainingCategoryRepository->update($trainingCategoryEntity, $updatedDto);
    }

    public function test_delete_training_category_deletes_record(): void
    {
        $trainingCategory = TrainingCategory::factory()->create(['name' => 'Safety Training']);
        $trainingCategoryEntity = $this->trainingCategoryRepository->findById($trainingCategory->id);

        $result = $this->trainingCategoryRepository->delete($trainingCategoryEntity);

        $this->assertTrue($result);
        $this->assertSoftDeleted('trainings_categories', [
            'id' => $trainingCategory->id,
        ]);
    }

    public function test_delete_returns_false_if_not_found(): void
    {
        $trainingCategoryEntity = new TrainingCategoryEntity(
            id: 'non-existent-uuid',
            name: 'Safety Training',
            createdAt: now(),
            updatedAt: now()
        );

        $result = $this->trainingCategoryRepository->delete($trainingCategoryEntity);

        $this->assertFalse($result);
    }
}
