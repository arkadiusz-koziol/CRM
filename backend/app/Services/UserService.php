<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserService
{

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function updateUser(User $user, array $data): bool
    {
        return $this->userRepository->update($user, $data);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }

    public function changePassword(User $user, string $newPassword): bool
    {
        return $this->userRepository->update($user, ['password' => $newPassword]);
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->list();
    }
}
