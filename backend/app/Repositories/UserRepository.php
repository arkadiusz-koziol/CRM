<?php
namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
