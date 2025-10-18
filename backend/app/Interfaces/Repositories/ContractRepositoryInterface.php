<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Billing\Entity\Contract;

interface ContractRepositoryInterface
{
    public function findById(string $id): ?Contract;

    public function findByNumber(string $number): ?Contract;

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array;

    public function save(Contract $contract): void;

    public function update(Contract $contract): void;

    public function delete(string $id): void;

    public function bulkUpdateStatus(array $ids, string $status): int;

    public function findByCompanyId(string $companyId): array;

    public function findExpired(): array;

    public function findExpiring(int $days = 30): array;
}
