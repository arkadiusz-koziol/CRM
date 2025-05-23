<?php
namespace App\Repositories;

use App\Enums\UserStatus;
use App\Interfaces\Repositories\UserRepositoryInterface;
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

    public function changePassword(User $user, string $newPassword): bool
    {
        return $user->update(['password' => $newPassword]);
    }

    public function list(): array
    {
        return User::all()->toArray();
    }

    public function userCanPerformAction(User $user): bool
    {
        return in_array($user->status,
            [
                UserStatus::ACTIVE->value,
            ]);
    }
}
