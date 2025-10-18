<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {}

    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    public function findAll(): array
    {
        return $this->model->all()->toArray();
    }

    public function findByRole(string $role): array
    {
        return $this->model->role($role)->get()->toArray();
    }

    public function findByIds(array $ids): array
    {
        return $this->model->whereIn('id', $ids)->get()->toArray();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function list(): array
    {
        return $this->model->all()->toArray();
    }
}
