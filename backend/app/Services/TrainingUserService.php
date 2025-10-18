<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Interfaces\Repositories\TrainingUserRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\TrainingUser;
use RuntimeException;

class TrainingUserService
{
    public function __construct(
        private TrainingUserRepositoryInterface $trainingUserRepository,
        private TrainingRepositoryInterface $trainingRepository,
        private UserRepositoryInterface $userRepository,
    ) {}

    public function assignUserToTraining(int $trainingId, int $userId): TrainingUser
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new RuntimeException('Training not found');
        }

        $user = $this->userRepository->findById($userId);
        if (! $user) {
            throw new RuntimeException('User not found');
        }

        if ($this->trainingUserRepository->isUserAssignedToTraining($training, $user)) {
            throw new RuntimeException('User is already assigned to this training');
        }

        return $this->trainingUserRepository->assignUserToTraining($training, $user);
    }

    public function removeUserFromTraining(int $trainingId, int $userId): bool
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new RuntimeException('Training not found');
        }

        $user = $this->userRepository->findById($userId);
        if (! $user) {
            throw new RuntimeException('User not found');
        }

        return $this->trainingUserRepository->removeUserFromTraining($training, $user);
    }

    public function assignAllUsersToTraining(int $trainingId): int
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new RuntimeException('Training not found');
        }

        return $this->trainingUserRepository->assignAllUsersToTraining($training);
    }

    public function assignUsersByRoleToTraining(int $trainingId, string $role): int
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new RuntimeException('Training not found');
        }

        return $this->trainingUserRepository->assignUsersByRoleToTraining($training, $role);
    }

    public function assignSelectedUsersToTraining(int $trainingId, array $userIds): int
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new RuntimeException('Training not found');
        }

        if (empty($userIds)) {
            throw new RuntimeException('No user IDs provided');
        }

        return $this->trainingUserRepository->assignSelectedUsersToTraining($training, $userIds);
    }

    public function removeAllUsersFromTraining(int $trainingId): int
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new RuntimeException('Training not found');
        }

        return $this->trainingUserRepository->removeAllUsersFromTraining($training);
    }

    public function getTrainingUsers(int $trainingId): array
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new RuntimeException('Training not found');
        }

        return $this->trainingUserRepository->getTrainingUsers($training);
    }

    public function getUserTrainings(int $userId): array
    {
        $user = $this->userRepository->findById($userId);
        if (! $user) {
            throw new RuntimeException('User not found');
        }

        return $this->trainingUserRepository->getUserTrainings($user);
    }

    public function isUserAssignedToTraining(int $trainingId, int $userId): bool
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            return false;
        }

        $user = $this->userRepository->findById($userId);
        if (! $user) {
            return false;
        }

        return $this->trainingUserRepository->isUserAssignedToTraining($training, $user);
    }

    public function getTrainingUserCount(int $trainingId): int
    {
        $training = $this->trainingRepository->findById($trainingId);
        if (! $training) {
            throw new RuntimeException('Training not found');
        }

        return $this->trainingUserRepository->getTrainingUserCount($training);
    }
}
