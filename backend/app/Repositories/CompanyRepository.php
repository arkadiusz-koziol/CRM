<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Crm\Entity\Company as CompanyEntity;
use App\Infrastructure\Company\CompanyMapper;
use App\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

class CompanyRepository extends EloquentRepository implements CompanyRepositoryInterface
{
    public function __construct(
        Company $model,
        private readonly CompanyMapper $mapper
    ) {
        parent::__construct($model);
    }

    public function findCompanyById(string $id): ?CompanyEntity
    {
        $model = $this->model->find($id);

        if (! $model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function findByVatId(string $vatId): ?CompanyEntity
    {
        $model = $this->model->where('vat_id', $vatId)->first();

        if (! $model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function findByName(string $name): ?CompanyEntity
    {
        $model = $this->model->where('name', $name)->first();

        if (! $model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function search(array $filters, int $perPage = 15, int $page = 1): array
    {
        $query = $this->model->newQuery();

        // Apply filters
        if (isset($filters['name'])) {
            $query->where('name', 'ILIKE', '%'.$filters['name'].'%');
        }

        if (isset($filters['vat_id'])) {
            $query->where('vat_id', 'ILIKE', '%'.$filters['vat_id'].'%');
        }

        if (isset($filters['region'])) {
            $query->where('region', 'ILIKE', '%'.$filters['region'].'%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (isset($filters['industry'])) {
            $query->where('industry', 'ILIKE', '%'.$filters['industry'].'%');
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $paginatedResults = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginatedResults->items(),
            'pagination' => [
                'current_page' => $paginatedResults->currentPage(),
                'per_page' => $paginatedResults->perPage(),
                'total' => $paginatedResults->total(),
                'last_page' => $paginatedResults->lastPage(),
                'from' => $paginatedResults->firstItem(),
                'to' => $paginatedResults->lastItem(),
            ],
        ];
    }

    public function findByUser(string $userId, array $filters = [], int $perPage = 15, int $page = 1): array
    {
        $query = $this->model->newQuery()
            ->whereHas('users', function (Builder $q) use ($userId) {
                $q->where('user_id', $userId);
            });

        // Apply the same filters as search
        if (isset($filters['name'])) {
            $query->where('name', 'ILIKE', '%'.$filters['name'].'%');
        }

        if (isset($filters['vat_id'])) {
            $query->where('vat_id', 'ILIKE', '%'.$filters['vat_id'].'%');
        }

        if (isset($filters['region'])) {
            $query->where('region', 'ILIKE', '%'.$filters['region'].'%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (isset($filters['industry'])) {
            $query->where('industry', 'ILIKE', '%'.$filters['industry'].'%');
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $paginatedResults = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginatedResults->items(),
            'pagination' => [
                'current_page' => $paginatedResults->currentPage(),
                'per_page' => $paginatedResults->perPage(),
                'total' => $paginatedResults->total(),
                'last_page' => $paginatedResults->lastPage(),
                'from' => $paginatedResults->firstItem(),
                'to' => $paginatedResults->lastItem(),
            ],
        ];
    }

    public function save(CompanyEntity $company): void
    {
        $existingModel = $this->model->find($company->id());

        if ($existingModel) {
            // Update existing record
            $existingModel->update([
                'name' => $company->name(),
                'industry' => $company->industry(),
                'source' => $company->source()->value,
                'status' => $company->status()->value,
                'region' => $company->region(),
                'vat_id' => $company->vatId(),
                'created_by' => $company->createdBy(),
                'updated_at' => $company->updatedAt(),
            ]);
        } else {
            // Create new record
            $model = $this->mapper->toModel($company);
            $model->save();
        }
    }

    public function deleteCompany(string $id): void
    {
        $this->model->where('id', $id)->delete();
    }

    public function restore(string $id): void
    {
        $this->model->withTrashed()->where('id', $id)->restore();
    }

    public function assignAccountManager(string $companyId, int $userId, string $role = 'account_manager'): void
    {
        $this->model->find($companyId)->users()->syncWithoutDetaching([
            $userId => ['role' => $role, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function removeAccountManager(string $companyId, int $userId): void
    {
        $this->model->find($companyId)->users()->detach($userId);
    }

    public function getAccountManagers(string $companyId): array
    {
        $company = $this->model->find($companyId);

        return $company->users()->get()->toArray();
    }

    private function mapToEntity(Company $model): CompanyEntity
    {
        return $this->mapper->toDomain($model);
    }
}
