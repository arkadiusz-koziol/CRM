<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Training;
use App\Models\TrainingFile;
use App\Repositories\TrainingFileRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TrainingFileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TrainingFileRepository $trainingFileRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trainingFileRepository = new TrainingFileRepository(new TrainingFile);
    }

    public function test_create_training_file(): void
    {
        $training = Training::factory()->create();
        $trainingFile = new TrainingFile([
            'training_id' => $training->id,
            'original_name' => 'test.pdf',
            'file_name' => 'uuid.pdf',
            'file_path' => 'training-files/uuid.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1000,
        ]);

        $result = $this->trainingFileRepository->create($trainingFile);

        $this->assertInstanceOf(TrainingFile::class, $result);
        $this->assertDatabaseHas('training_files', [
            'training_id' => $training->id,
            'original_name' => 'test.pdf',
        ]);
    }

    public function test_find_by_id_returns_training_file_when_exists(): void
    {
        $trainingFile = TrainingFile::factory()->create();

        $result = $this->trainingFileRepository->findById($trainingFile->id);

        $this->assertInstanceOf(TrainingFile::class, $result);
        $this->assertEquals($trainingFile->id, $result->id);
    }

    public function test_find_by_id_returns_null_when_not_exists(): void
    {
        $result = $this->trainingFileRepository->findById('non-existent-id');

        $this->assertNull($result);
    }

    public function test_find_by_training_returns_collection(): void
    {
        $training = Training::factory()->create();
        $files = TrainingFile::factory()->count(3)->create(['training_id' => $training->id]);

        $result = $this->trainingFileRepository->findByTraining($training);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_delete_training_file(): void
    {
        $trainingFile = TrainingFile::factory()->create();

        $result = $this->trainingFileRepository->delete($trainingFile);

        $this->assertTrue($result);
        $this->assertSoftDeleted('training_files', [
            'id' => $trainingFile->id,
        ]);
    }

    public function test_delete_by_training_returns_count(): void
    {
        $training = Training::factory()->create();
        $files = TrainingFile::factory()->count(3)->create(['training_id' => $training->id]);

        $result = $this->trainingFileRepository->deleteByTraining($training);

        $this->assertEquals(3, $result);
        $this->assertSoftDeleted('training_files', [
            'training_id' => $training->id,
        ]);
    }
}
