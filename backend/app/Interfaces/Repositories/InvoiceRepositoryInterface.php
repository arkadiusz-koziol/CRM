<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Billing\Entity\Invoice;

interface InvoiceRepositoryInterface
{
    public function findById(string $id): ?Invoice;

    public function findByNumber(string $number): ?Invoice;

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array;

    public function save(Invoice $invoice): void;

    public function update(Invoice $invoice): void;

    public function delete(string $id): void;

    public function bulkUpdateStatus(array $ids, string $status): int;

    public function findByCompanyId(string $companyId): array;

    public function findByContractId(string $contractId): array;

    public function findOverdue(): array;

    public function findDueSoon(int $days = 7): array;
}
