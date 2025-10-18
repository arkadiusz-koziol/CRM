<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Dto\TrainingDto;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Models\Training;
use App\Services\TrainingService;
use Mockery;
use Tests\TestCase;

final class TrainingServiceTest extends TestCase
{
    private TrainingService $trainingService;

    private $trainingRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trainingRepositoryMock = Mockery::mock(TrainingRepositoryInterface::class);
        $this->trainingService = new TrainingService($this->trainingRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_training_calls_repository(): void
    {
        $trainingDto = new TrainingDto(
            title: 'Test Training',
            description: 'Test Description',
            category: 'Test Category',
            categoryId: 'test-category-id',
            filePath: null,
            fileName: null,
            fileSize: null,
            mimeType: null
        );

        $training = new Training;
        $training->id = 1;
        $training->title = 'Test Training';

        $this->trainingRepositoryMock
            ->shouldReceive('createTraining')
            ->once()
            ->with($trainingDto)
            ->andReturn($training);

        $result = $this->trainingService->createTraining($trainingDto);

        $this->assertSame($training, $result);
    }

    public function test_get_training_by_id_calls_repository(): void
    {
        $training = new Training;
        $training->id = 1;
        $training->title = 'Test Training';

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($training);

        $result = $this->trainingService->getTrainingById(1);

        $this->assertSame($training, $result);
    }

    public function test_get_training_by_id_returns_null_when_not_found(): void
    {
        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->trainingService->getTrainingById(999);

        $this->assertNull($result);
    }

    public function test_get_all_trainings_calls_repository(): void
    {
        $trainings = [
            ['id' => 1, 'title' => 'Training 1'],
            ['id' => 2, 'title' => 'Training 2'],
        ];

        $this->trainingRepositoryMock
            ->shouldReceive('findAllTrainings')
            ->once()
            ->andReturn($trainings);

        $result = $this->trainingService->getAllTrainings();

        $this->assertSame($trainings, $result);
    }

    public function test_get_paginated_trainings_calls_repository(): void
    {
        $paginatedData = [
            'data' => [
                ['id' => 1, 'title' => 'Training 1'],
                ['id' => 2, 'title' => 'Training 2'],
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

        $this->trainingRepositoryMock
            ->shouldReceive('findPaginated')
            ->once()
            ->with(1, 10, '')
            ->andReturn($paginatedData);

        $result = $this->trainingService->getPaginatedTrainings(1, 10, '');

        $this->assertSame($paginatedData, $result);
    }

    public function test_get_paginated_trainings_with_search_calls_repository(): void
    {
        $paginatedData = [
            'data' => [
                ['id' => 1, 'title' => 'Safety Training'],
            ],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 1,
            ],
        ];

        $this->trainingRepositoryMock
            ->shouldReceive('findPaginated')
            ->once()
            ->with(1, 10, 'safety')
            ->andReturn($paginatedData);

        $result = $this->trainingService->getPaginatedTrainings(1, 10, 'safety');

        $this->assertSame($paginatedData, $result);
    }

    public function test_update_training_calls_repository(): void
    {
        $training = new Training;
        $training->id = 1;
        $training->title = 'Original Training';

        $trainingDto = new TrainingDto(
            title: 'Updated Training',
            description: 'Updated Description',
            category: 'Updated Category',
            categoryId: 'test-category-id',
            filePath: 'trainings/updated.pdf',
            fileName: 'updated.pdf',
            fileSize: 2048,
            mimeType: 'application/pdf'
        );

        $this->trainingRepositoryMock
            ->shouldReceive('updateTraining')
            ->once()
            ->with($training, $trainingDto)
            ->andReturn(true);

        $result = $this->trainingService->updateTraining($training, $trainingDto);

        $this->assertTrue($result);
    }

    public function test_update_training_returns_false_when_repository_fails(): void
    {
        $training = new Training;
        $training->id = 1;
        $training->title = 'Original Training';

        $trainingDto = new TrainingDto(
            title: 'Updated Training',
            description: 'Updated Description',
            category: 'Updated Category',
            categoryId: 'test-category-id',
            filePath: null,
            fileName: null,
            fileSize: null,
            mimeType: null
        );

        $this->trainingRepositoryMock
            ->shouldReceive('updateTraining')
            ->once()
            ->with($training, $trainingDto)
            ->andReturn(false);

        $result = $this->trainingService->updateTraining($training, $trainingDto);

        $this->assertFalse($result);
    }

    public function test_delete_training_calls_repository(): void
    {
        $training = new Training;
        $training->id = 1;
        $training->title = 'Training to Delete';

        $this->trainingRepositoryMock
            ->shouldReceive('deleteTraining')
            ->once()
            ->with($training)
            ->andReturn(true);

        $result = $this->trainingService->deleteTraining($training);

        $this->assertTrue($result);
    }

    public function test_delete_training_returns_false_when_repository_fails(): void
    {
        $training = new Training;
        $training->id = 1;
        $training->title = 'Training to Delete';

        $this->trainingRepositoryMock
            ->shouldReceive('deleteTraining')
            ->once()
            ->with($training)
            ->andReturn(false);

        $result = $this->trainingService->deleteTraining($training);

        $this->assertFalse($result);
    }
}
