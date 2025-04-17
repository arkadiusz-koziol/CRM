<?php

namespace App\Interfaces\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function update(User $user, array $data): bool;

    public function findById(int $id): ?User;

    public function delete(User $user): bool;

    public function changePassword(User $user, string $newPassword): bool;
    public function list(): array;

    public function getAllIds(): Collection;

    public function getByRoles(array $roleIds): Collection;

    public function getByIds(array $userIds): Collection;
}
