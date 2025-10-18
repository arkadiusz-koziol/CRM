<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\TrainingUserRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class TrainingUserRepository extends EloquentRepository implements TrainingUserRepositoryInterface
{
    public function __construct(
        TrainingUser $model,
        private UserRepositoryInterface $userRepository
    ) {
        parent::__construct($model);
    }

    public function assignUserToTraining(Training $training, User $user): TrainingUser
    {
        return $this->model->create([
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);
    }

    public function removeUserFromTraining(Training $training, User $user): bool
    {
        return $this->model
            ->where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->forceDelete() > 0;
    }

    public function assignAllUsersToTraining(Training $training): int
    {
        $users = $this->userRepository->findAll();
        $assignments = [];
        $now = Carbon::now();

        foreach ($users as $user) {
            $assignments[] = [
                'id' => Uuid::uuid4()->toString(),
                'training_id' => $training->id,
                'user_id' => $user['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($assignments)) {
            return 0;
        }

        return $this->model->insert($assignments) ? count($assignments) : 0;
    }

    public function assignUsersByRoleToTraining(Training $training, string $role): int
    {
        $users = $this->userRepository->findByRole($role);
        $assignments = [];
        $now = Carbon::now();

        foreach ($users as $user) {
            $assignments[] = [
                'id' => Uuid::uuid4()->toString(),
                'training_id' => $training->id,
                'user_id' => $user['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($assignments)) {
            return 0;
        }

        return $this->model->insert($assignments) ? count($assignments) : 0;
    }

    public function assignSelectedUsersToTraining(Training $training, array $userIds): int
    {
        $assignments = [];
        $now = Carbon::now();

        foreach ($userIds as $userId) {
            $assignments[] = [
                'id' => Uuid::uuid4()->toString(),
                'training_id' => $training->id,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($assignments)) {
            return 0;
        }

        return $this->model->insert($assignments) ? count($assignments) : 0;
    }

    public function removeAllUsersFromTraining(Training $training): int
    {
        return $this->model
            ->where('training_id', $training->id)
            ->forceDelete();
    }

    public function getTrainingUsers(Training $training): array
    {
        return $this->model
            ->where('training_id', $training->id)
            ->with('user')
            ->get()
            ->toArray();
    }

    public function getUserTrainings(User $user): array
    {
        return $this->model
            ->where('user_id', $user->id)
            ->with('training')
            ->get()
            ->toArray();
    }

    public function isUserAssignedToTraining(Training $training, User $user): bool
    {
        return $this->model
            ->where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function getTrainingUserCount(Training $training): int
    {
        return $this->model
            ->where('training_id', $training->id)
            ->count();
    }
}
