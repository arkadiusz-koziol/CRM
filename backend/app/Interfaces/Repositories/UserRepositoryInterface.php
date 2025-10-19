<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Interfaces\Domain\User\UserInterface;
use Ramsey\Uuid\UuidInterface;

interface UserRepositoryInterface
{
    public function findById(UuidInterface $id): ?UserInterface;

    public function findByIntId(int $id): ?UserInterface;

    public function findAll(): array;

    public function findByRole(string $role): array;

    public function findByIds(array $ids): array;

    public function create(array $data): UserInterface;

    public function update(UserInterface $user, array $data): bool;

    public function delete(UserInterface $user): bool;

    public function list(): array;

    public function findByUsername(string $username): ?UserInterface;

    public function hasOptedOutOfMentions(UuidInterface $userId): bool;

    public function hasOptedOutOfMentionEmails(UuidInterface $userId): bool;
}
