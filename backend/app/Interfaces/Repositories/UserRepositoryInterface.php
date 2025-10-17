<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findAll(): array;

    public function findByRole(string $role): array;

    public function findByIds(array $ids): array;
}
