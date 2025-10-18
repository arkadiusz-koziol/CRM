<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Interfaces\Repositories\TrainingFileRepositoryInterface;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Models\Training;
use App\Models\TrainingFile;
use App\Services\TrainingFileService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

final class TrainingFileServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingFileService $trainingFileService;

    private $trainingFileRepositoryMock;

    private $trainingRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->trainingFileRepositoryMock = Mockery::mock(TrainingFileRepositoryInterface::class);
        $this->trainingRepositoryMock = Mockery::mock(TrainingRepositoryInterface::class);

        $this->trainingFileService = new TrainingFileService(
            $this->trainingFileRepositoryMock,
            $this->trainingRepositoryMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_attach_file_to_training_calls_repository(): void
    {
        $training = Training::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');
        $trainingFile = new TrainingFile;

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $trainingFileEntity = new \App\Domain\TrainingFile\Entity\TrainingFile(
            id: '1',
            trainingId: (string) $training->id,
            originalName: 'test.pdf',
            fileName: 'test.pdf',
            filePath: '/path/to/test.pdf',
            mimeType: 'application/pdf',
            fileSize: 100,
            createdAt: now(),
            updatedAt: now()
        );

        $this->trainingFileRepositoryMock
            ->shouldReceive('attachFileToTraining')
            ->once()
            ->with($training, $file)
            ->andReturn($trainingFileEntity);

        $result = $this->trainingFileService->attachFileToTraining($training->id, $file);

        $this->assertInstanceOf(\App\Domain\TrainingFile\Entity\TrainingFile::class, $result);
    }

    public function test_attach_file_to_training_throws_exception_when_training_not_found(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Training not found');

        $this->trainingFileService->attachFileToTraining(999, $file);
    }

    public function test_attach_multiple_files_to_training_calls_repository(): void
    {
        $training = Training::factory()->create();
        $files = [
            UploadedFile::fake()->create('document1.pdf', 1000, 'application/pdf'),
            UploadedFile::fake()->create('document2.pdf', 2000, 'application/pdf'),
        ];

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $trainingFileEntity1 = new \App\Domain\TrainingFile\Entity\TrainingFile(
            id: '1',
            trainingId: (string) $training->id,
            originalName: 'test1.pdf',
            fileName: 'test1.pdf',
            filePath: '/path/to/test1.pdf',
            mimeType: 'application/pdf',
            fileSize: 100,
            createdAt: now(),
            updatedAt: now()
        );

        $trainingFileEntity2 = new \App\Domain\TrainingFile\Entity\TrainingFile(
            id: '2',
            trainingId: (string) $training->id,
            originalName: 'test2.pdf',
            fileName: 'test2.pdf',
            filePath: '/path/to/test2.pdf',
            mimeType: 'application/pdf',
            fileSize: 200,
            createdAt: now(),
            updatedAt: now()
        );

        $this->trainingFileRepositoryMock
            ->shouldReceive('attachMultipleFilesToTraining')
            ->once()
            ->with($training, $files)
            ->andReturn(new Collection([$trainingFileEntity1, $trainingFileEntity2]));

        $result = $this->trainingFileService->attachMultipleFilesToTraining($training->id, $files);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function test_get_training_files_calls_repository(): void
    {
        $training = Training::factory()->create();
        $files = new Collection([new TrainingFile, new TrainingFile]);

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->trainingFileRepositoryMock
            ->shouldReceive('getTrainingFiles')
            ->once()
            ->with($training)
            ->andReturn($files);

        $result = $this->trainingFileService->getTrainingFiles($training->id);

        $this->assertSame($files, $result);
    }

    public function test_delete_training_file_calls_repository(): void
    {
        $training = Training::factory()->create();
        $file = TrainingFile::factory()->create(['training_id' => $training->id]);

        $trainingFileEntity = new \App\Domain\TrainingFile\Entity\TrainingFile(
            id: (string) $file->id,
            trainingId: (string) $file->training_id,
            originalName: 'test.pdf',
            fileName: 'test.pdf',
            filePath: '/path/to/test.pdf',
            mimeType: 'application/pdf',
            fileSize: 100,
            createdAt: now(),
            updatedAt: now()
        );

        $this->trainingFileRepositoryMock
            ->shouldReceive('findTrainingFileById')
            ->once()
            ->with($file->id)
            ->andReturn($trainingFileEntity);

        $this->trainingFileRepositoryMock
            ->shouldReceive('deleteTrainingFile')
            ->once()
            ->with(Mockery::type(\App\Domain\TrainingFile\Entity\TrainingFile::class))
            ->andReturn(true);

        $result = $this->trainingFileService->deleteTrainingFile($file->id);

        $this->assertTrue($result);
    }

    public function test_delete_training_file_throws_exception_when_file_not_found(): void
    {
        $this->trainingFileRepositoryMock
            ->shouldReceive('findTrainingFileById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Training file not found');

        $this->trainingFileService->deleteTrainingFile(999);
    }
}
