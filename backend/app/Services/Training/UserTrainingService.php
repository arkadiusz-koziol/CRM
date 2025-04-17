<?php

namespace App\Services\Training;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\Training;
use App\Enums\AssignmentByRoleType;

readonly class UserTrainingService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function assignUsers(Training $training, array $data): void
    {
        $userIds = match (AssignmentByRoleType::fromRequest($data['assignment_type'])) {
            AssignmentByRoleType::ALL => $this->userRepository->getAllIds(),
            AssignmentByRoleType::ROLE => $this->userRepository->getByRoles($data['role_ids']),
            AssignmentByRoleType::USERS => $this->userRepository->getByIds($data['user_ids']),
        };

        $training->users()->sync($userIds);
    }
}
