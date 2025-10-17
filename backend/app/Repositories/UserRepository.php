<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;

final class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {
        parent::__construct($model);
    }

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
}
