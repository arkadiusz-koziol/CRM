<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\User\Entity\User as UserEntity;
use App\Infrastructure\User\UserMapper;
use App\Interfaces\Domain\User\UserInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use Ramsey\Uuid\UuidInterface;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {}

    public function findById(UuidInterface $id): ?UserInterface
    {
        $model = $this->model->find($id->toString());

        return $model ? UserMapper::toDomain($model) : null;
    }

    public function findByIntId(int $id): ?UserInterface
    {
        $model = $this->model->find($id);

        return $model ? UserMapper::toDomain($model) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn (User $model) => UserMapper::toDomain($model))->toArray();
    }

    public function findByRole(string $role): array
    {
        return $this->model->role($role)->get()->map(fn (User $model) => UserMapper::toDomain($model))->toArray();
    }

    public function findByIds(array $ids): array
    {
        return $this->model->whereIn('id', $ids)->get()->map(fn (User $model) => UserMapper::toDomain($model))->toArray();
    }

    public function create(array $data): UserEntity
    {
        $model = $this->model->create($data);

        return UserMapper::toDomain($model);
    }

    public function update(UserInterface $user, array $data): bool
    {
        $model = $this->model->find($user->intId());

        return $model ? $model->update($data) : false;
    }

    public function delete(UserInterface $user): bool
    {
        $model = $this->model->find($user->intId());

        return $model ? $model->delete() : false;
    }

    public function list(): array
    {
        return $this->model->all()->map(fn (User $model) => UserMapper::toDomain($model))->toArray();
    }

    public function findByUsername(string $username): ?UserInterface
    {
        $model = $this->model->where('username', $username)->first();

        return $model ? UserMapper::toDomain($model) : null;
    }

    public function hasOptedOutOfMentions(UuidInterface $userId): bool
    {
        $user = $this->findById($userId);

        return $user ? $user->hasOptedOutOfMentions() : true;
    }

    public function hasOptedOutOfMentionEmails(UuidInterface $userId): bool
    {
        $user = $this->findById($userId);

        return $user ? $user->hasOptedOutOfMentionEmails() : true;
    }
}
