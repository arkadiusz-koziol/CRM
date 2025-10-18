<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;

interface TrainingUserRepositoryInterface
{
    public function assignUserToTraining(Training $training, User $user): TrainingUser;

    public function removeUserFromTraining(Training $training, User $user): bool;

    public function assignAllUsersToTraining(Training $training): int;

    public function assignUsersByRoleToTraining(Training $training, string $role): int;

    public function assignSelectedUsersToTraining(Training $training, array $userIds): int;

    public function removeAllUsersFromTraining(Training $training): int;

    public function getTrainingUsers(Training $training): array;

    public function getUserTrainings(User $user): array;

    public function isUserAssignedToTraining(Training $training, User $user): bool;

    public function getTrainingUserCount(Training $training): int;
}
