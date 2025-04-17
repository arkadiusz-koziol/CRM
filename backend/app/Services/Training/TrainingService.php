<?php

namespace App\Services\Training;

use App\Config\TrainingConfig;
use App\Interfaces\Repositories\TrainingFileRepositoryInterface;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Models\Training;
use App\Services\FileService;
use Illuminate\Database\DatabaseManager;
use Throwable;

readonly class TrainingService
{
    public function __construct(
        private TrainingRepositoryInterface $trainingRepository,
        private UserTrainingService $userTrainingService,
        private TrainingFileRepositoryInterface $trainingFileRepository,
        private FileService $fileService,
        private DatabaseManager $db,
        private TrainingConfig $trainingConfig,
    ) {}

    /**
     * @throws Throwable
     */
    public function store(array $data): Training
    {
        return $this->db->transaction(function () use ($data) {
            $training = $this->trainingRepository->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'],
            ]);

            $this->userTrainingService->assignUsers($training, $data);
            $this->storeTrainingFiles($training, $data['files']);

            return $training;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(Training $training, array $data): Training
    {
        return $this->db->transaction(function () use ($training, $data) {
            $this->trainingRepository->update($training, [
                'title' => $data['title'] ?? $training->getTitle(),
                'description' => $data['description'] ?? $training->getDescription(),
                'category_id' => $data['category_id'] ?? $training->getCategoryId(),
            ]);

            $training->users()->detach();
            $this->userTrainingService->assignUsers($training, $data);
            $this->storeTrainingFiles($training, $data['files']);

            return $training;
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(Training $training): bool
    {
        return $this->db->transaction(function () use ($training) {
            $training->users()->detach();

            foreach ($this->trainingFileRepository->all($training) as $file) {
                $this->fileService->delete($file->file_path);
            }
            $this->trainingFileRepository->deleteAll($training);

            return $this->trainingRepository->delete($training);
        });
    }

    private function storeTrainingFiles(Training $training, array $data): void
    {
        $files = $data['files'];
        if (!empty($files)) {
            foreach ($files as $file) {
                $storedPath = $this->fileService->store($file, $this->trainingConfig->getFilesPath());
                $this->trainingFileRepository->create($training, $storedPath);
            }
        }
    }
}
