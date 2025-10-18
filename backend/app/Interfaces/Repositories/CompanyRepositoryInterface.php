<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Crm\Entity\Company;
use App\Interfaces\Repositories\Base\RepositoryInterface;

interface CompanyRepositoryInterface extends RepositoryInterface
{
    public function findCompanyById(string $id): ?Company;

    public function findByVatId(string $vatId): ?Company;

    public function findByName(string $name): ?Company;

    public function search(array $filters, int $perPage = 15, int $page = 1): array;

    public function findByUser(string $userId, array $filters = [], int $perPage = 15, int $page = 1): array;

    public function save(Company $company): void;

    public function deleteCompany(string $id): void;

    public function restore(string $id): void;

    public function assignAccountManager(string $companyId, int $userId, string $role = 'account_manager'): void;

    public function removeAccountManager(string $companyId, int $userId): void;

    public function getAccountManagers(string $companyId): array;
}
