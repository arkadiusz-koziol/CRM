<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Crm\Entity\Company;
use App\Enums\Crm\CompanySource;
use App\Enums\Crm\CompanyStatus;
use App\Interfaces\Repositories\CompanyRepositoryInterface;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;

class CompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private LoggerInterface $logger
    ) {}

    public function findById(string $id): ?Company
    {
        return $this->companyRepository->findCompanyById($id);
    }

    public function search(array $filters, int $perPage = 15, int $page = 1): array
    {
        // Validate pagination limits
        $perPage = max(1, min(100, $perPage));
        $page = max(1, $page);

        return $this->companyRepository->search($filters, $perPage, $page);
    }

    public function findByUser(string $userId, array $filters = [], int $perPage = 15, int $page = 1): array
    {
        // Validate pagination limits
        $perPage = max(1, min(100, $perPage));
        $page = max(1, $page);

        return $this->companyRepository->findByUser($userId, $filters, $perPage, $page);
    }

    public function create(array $data, string $createdBy): Company
    {
        // Check for duplicate VAT ID if provided
        if (isset($data['vat_id']) && $data['vat_id']) {
            $existingCompany = $this->companyRepository->findByVatId($data['vat_id']);
            if ($existingCompany) {
                throw new \InvalidArgumentException('Company with this VAT ID already exists.');
            }
        }

        $company = Company::create(
            name: $data['name'],
            industry: $data['industry'] ?? null,
            source: CompanySource::from($data['source']),
            status: CompanyStatus::from($data['status'] ?? CompanyStatus::PROSPECT->value),
            region: $data['region'] ?? null,
            vatId: $data['vat_id'] ?? null,
            createdBy: $createdBy
        );

        $this->companyRepository->save($company);

        $this->logger->info('Company created', [
            'company_id' => $company->id(),
            'company_name' => $company->name(),
            'created_by' => $createdBy,
        ]);

        return $company;
    }

    public function update(string $id, array $data): Company
    {
        $company = $this->companyRepository->findCompanyById($id);
        if (! $company) {
            throw new \InvalidArgumentException('Company not found.');
        }

        // Check for duplicate VAT ID if provided and different from current
        if (isset($data['vat_id']) && $data['vat_id'] && $data['vat_id'] !== $company->vatId()) {
            $existingCompany = $this->companyRepository->findByVatId($data['vat_id']);
            if ($existingCompany && $existingCompany->id() !== $company->id()) {
                throw new \InvalidArgumentException('Company with this VAT ID already exists.');
            }
        }

        // Create updated company entity with same ID
        $updatedCompany = new Company(
            id: $company->id(),
            name: $data['name'] ?? $company->name(),
            industry: $data['industry'] ?? $company->industry(),
            source: isset($data['source']) ? CompanySource::from($data['source']) : $company->source(),
            status: isset($data['status']) ? CompanyStatus::from($data['status']) : $company->status(),
            region: $data['region'] ?? $company->region(),
            vatId: $data['vat_id'] ?? $company->vatId(),
            createdBy: $company->createdBy(),
            createdAt: $company->createdAt(),
            updatedAt: Carbon::now()
        );

        $this->companyRepository->save($updatedCompany);

        $this->logger->info('Company updated', [
            'company_id' => $updatedCompany->id(),
            'company_name' => $updatedCompany->name(),
        ]);

        return $updatedCompany;
    }

    public function delete(string $id): void
    {
        $company = $this->companyRepository->findCompanyById($id);
        if (! $company) {
            throw new \InvalidArgumentException('Company not found.');
        }

        $this->companyRepository->deleteCompany($id);

        $this->logger->info('Company deleted', [
            'company_id' => $id,
            'company_name' => $company->name(),
        ]);
    }

    public function restore(string $id): void
    {
        $this->companyRepository->restore($id);

        $this->logger->info('Company restored', [
            'company_id' => $id,
        ]);
    }

    public function assignUser(string $companyId, string $userId, string $role = 'account_manager'): void
    {
        $company = $this->companyRepository->findCompanyById($companyId);
        if (! $company) {
            throw new \InvalidArgumentException('Company not found.');
        }

        // Actually assign the user to the company via repository
        $this->companyRepository->assignAccountManager($companyId, (int) $userId, $role);

        $this->logger->info('User assigned to company', [
            'company_id' => $companyId,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function removeUser(string $companyId, string $userId): void
    {
        $company = $this->companyRepository->findCompanyById($companyId);
        if (! $company) {
            throw new \InvalidArgumentException('Company not found.');
        }

        // Actually remove the user from the company via repository
        $this->companyRepository->removeAccountManager($companyId, (int) $userId);

        $this->logger->info('User removed from company', [
            'company_id' => $companyId,
            'user_id' => $userId,
        ]);
    }

    public function getAccountManagers(string $companyId): array
    {
        $company = $this->companyRepository->findCompanyById($companyId);
        if (! $company) {
            throw new \InvalidArgumentException('Company not found.');
        }

        return $this->companyRepository->getAccountManagers($companyId);
    }
}
