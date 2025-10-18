<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Dto\TrainingDto;
use App\Models\Training;
use App\Repositories\TrainingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TrainingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TrainingRepository $trainingRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trainingRepository = new TrainingRepository(new Training);
    }

    public function test_create_training_creates_database_record(): void
    {
        $trainingDto = new TrainingDto(
            title: 'Test Training',
            description: 'Test Description',
            category: 'Test Category',
            filePath: null,
            fileName: null,
            fileSize: null,
            mimeType: null
        );

        $training = $this->trainingRepository->createTraining($trainingDto);

        $this->assertInstanceOf(Training::class, $training);
        $this->assertEquals('Test Training', $training->title);
        $this->assertEquals('Test Description', $training->description);
        $this->assertEquals('Test Category', $training->category);
        $this->assertNull($training->file_path);
        $this->assertNull($training->file_name);
        $this->assertNull($training->file_size);
        $this->assertNull($training->mime_type);

        $this->assertDatabaseHas('trainings', [
            'title' => 'Test Training',
            'description' => 'Test Description',
            'category' => 'Test Category',
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
        ]);
    }

    public function test_create_training_with_file_data(): void
    {
        $trainingDto = new TrainingDto(
            title: 'File Training',
            description: 'Training with file',
            category: 'File Category',
            filePath: 'trainings/test.pdf',
            fileName: 'test.pdf',
            fileSize: 1024,
            mimeType: 'application/pdf'
        );

        $training = $this->trainingRepository->createTraining($trainingDto);

        $this->assertInstanceOf(Training::class, $training);
        $this->assertEquals('File Training', $training->title);
        $this->assertEquals('trainings/test.pdf', $training->file_path);
        $this->assertEquals('test.pdf', $training->file_name);
        $this->assertEquals(1024, $training->file_size);
        $this->assertEquals('application/pdf', $training->mime_type);

        $this->assertDatabaseHas('trainings', [
            'title' => 'File Training',
            'description' => 'Training with file',
            'category' => 'File Category',
            'file_path' => 'trainings/test.pdf',
            'file_name' => 'test.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);
    }

    public function test_find_by_id_returns_training_when_exists(): void
    {
        $training = Training::factory()->create([
            'title' => 'Find Test Training',
            'description' => 'Training to find',
            'category' => 'Find Category',
        ]);

        $result = $this->trainingRepository->findById($training->id);

        $this->assertInstanceOf(Training::class, $result);
        $this->assertEquals($training->id, $result->id);
        $this->assertEquals('Find Test Training', $result->title);
        $this->assertEquals('Training to find', $result->description);
        $this->assertEquals('Find Category', $result->category);
    }

    public function test_find_by_id_returns_null_when_not_exists(): void
    {
        $result = $this->trainingRepository->findById(99999);

        $this->assertNull($result);
    }

    public function test_find_all_trainings_returns_all_trainings(): void
    {
        Training::factory()->create(['title' => 'Training 1']);
        Training::factory()->create(['title' => 'Training 2']);
        Training::factory()->create(['title' => 'Training 3']);

        $result = $this->trainingRepository->findAllTrainings();

        $this->assertCount(3, $result);
        $this->assertEquals('Training 1', $result[0]['title']);
        $this->assertEquals('Training 2', $result[1]['title']);
        $this->assertEquals('Training 3', $result[2]['title']);
    }

    public function test_find_paginated_returns_paginated_data(): void
    {
        Training::factory()->create(['title' => 'Training 1']);
        Training::factory()->create(['title' => 'Training 2']);

        $result = $this->trainingRepository->findPaginated(1, 10);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(2, $result['data']);
        $this->assertEquals(1, $result['pagination']['current_page']);
        $this->assertEquals(10, $result['pagination']['per_page']);
        $this->assertEquals(2, $result['pagination']['total']);
        $this->assertEquals(1, $result['pagination']['last_page']);
        $this->assertEquals(1, $result['pagination']['from']);
        $this->assertEquals(2, $result['pagination']['to']);
    }

    public function test_find_paginated_with_search_filters_results(): void
    {
        Training::factory()->create(['title' => 'Safety Training', 'category' => 'Safety']);
        Training::factory()->create(['title' => 'Technical Training', 'category' => 'Technical']);
        Training::factory()->create(['title' => 'Safety Protocol', 'category' => 'Safety']);

        $result = $this->trainingRepository->findPaginated(1, 10, 'safety');

        $this->assertCount(2, $result['data']);
        $this->assertEquals(2, $result['pagination']['total']);
    }

    public function test_find_paginated_with_empty_search_returns_all(): void
    {
        Training::factory()->create(['title' => 'Training 1']);
        Training::factory()->create(['title' => 'Training 2']);

        $result = $this->trainingRepository->findPaginated(1, 10, '');

        $this->assertCount(2, $result['data']);
        $this->assertEquals(2, $result['pagination']['total']);
    }

    public function test_find_paginated_with_different_page_sizes(): void
    {
        Training::factory()->count(5)->create();

        $result = $this->trainingRepository->findPaginated(1, 3);

        $this->assertCount(3, $result['data']);
        $this->assertEquals(3, $result['pagination']['per_page']);
        $this->assertEquals(5, $result['pagination']['total']);
        $this->assertEquals(2, $result['pagination']['last_page']);
    }

    public function test_find_paginated_with_search_in_description(): void
    {
        Training::factory()->create([
            'title' => 'Training 1',
            'description' => 'This is about safety procedures',
            'category' => 'Technical',
        ]);
        Training::factory()->create([
            'title' => 'Training 2',
            'description' => 'This is about technical skills',
            'category' => 'Technical',
        ]);

        $result = $this->trainingRepository->findPaginated(1, 10, 'safety');

        $this->assertCount(1, $result['data']);
        $this->assertEquals('Training 1', $result['data'][0]->title);
    }

    public function test_find_paginated_with_search_in_category(): void
    {
        Training::factory()->create(['title' => 'Training 1', 'category' => 'Safety']);
        Training::factory()->create(['title' => 'Training 2', 'category' => 'Technical']);

        $result = $this->trainingRepository->findPaginated(1, 10, 'technical');

        $this->assertCount(1, $result['data']);
        $this->assertEquals('Training 2', $result['data'][0]->title);
    }

    public function test_update_training_updates_database_record(): void
    {
        $training = Training::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'category' => 'Original Category',
        ]);

        $trainingDto = new TrainingDto(
            title: 'Updated Training',
            description: 'Updated Description',
            category: 'Updated Category',
            filePath: 'trainings/updated.pdf',
            fileName: 'updated.pdf',
            fileSize: 2048,
            mimeType: 'application/pdf'
        );

        $result = $this->trainingRepository->updateTraining($training, $trainingDto);

        $this->assertTrue($result);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'title' => 'Updated Training',
            'description' => 'Updated Description',
            'category' => 'Updated Category',
            'file_path' => 'trainings/updated.pdf',
            'file_name' => 'updated.pdf',
            'file_size' => 2048,
            'mime_type' => 'application/pdf',
        ]);
    }

    public function test_update_training_with_null_values(): void
    {
        $training = Training::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'category' => 'Original Category',
            'file_path' => 'trainings/original.pdf',
            'file_name' => 'original.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);

        $trainingDto = new TrainingDto(
            title: 'Updated Training',
            description: null,
            category: 'Updated Category',
            filePath: null,
            fileName: null,
            fileSize: null,
            mimeType: null
        );

        $result = $this->trainingRepository->updateTraining($training, $trainingDto);

        $this->assertTrue($result);

        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'title' => 'Updated Training',
            'description' => null,
            'category' => 'Updated Category',
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
        ]);
    }

    public function test_update_training_returns_false_on_database_error(): void
    {
        $training = Training::factory()->create();

        $trainingDto = new TrainingDto(
            title: 'Updated Training',
            description: 'Updated Description',
            category: 'Updated Category',
            filePath: null,
            fileName: null,
            fileSize: null,
            mimeType: null
        );

        // Mock the training instance to simulate a database error
        $trainingMock = $this->createMock(Training::class);
        $trainingMock->method('update')
            ->willThrowException(new \Exception('Database error'));

        $result = $this->trainingRepository->updateTraining($trainingMock, $trainingDto);

        $this->assertFalse($result);
    }
}
