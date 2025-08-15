<?php

namespace App\Services;

use App\Dto\CreateUserDto;
use App\Enums\UserRoles;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserService
{

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    public function createUser(CreateUserDto $dto): User
    {
        $data = [
            'name'       => $dto->getName(),
            'surname'    => $dto->getSurname(),
            'email'      => $dto->getEmail(),
            'phone'      => $dto->getPhone(),
            'password'   => $dto->getPassword(),
            'city'       => $dto->getCity(),
            'vovoidship' => $dto->getVovoidship(),
        ];

        $user = $this->userRepository->create($data);

        $user->assignRole(UserRoles::USER->value);

        return $user;
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
