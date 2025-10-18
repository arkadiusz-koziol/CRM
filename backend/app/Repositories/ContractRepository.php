<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Billing\Entity\Contract;
use App\Enums\Billing\ContractStatus;
use App\Infrastructure\Billing\ContractMapper;
use App\Interfaces\Repositories\ContractRepositoryInterface;
use App\Models\Contract as ContractModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

final class ContractRepository implements ContractRepositoryInterface
{
    public function __construct(
        private ContractMapper $mapper
    ) {}

    public function findById(string $id): ?Contract
    {
        // Validate UUID format before querying database
        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
            return null;
        }

        $model = ContractModel::find($id);

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findByNumber(string $number): ?Contract
    {
        $model = ContractModel::where('nr', $number)->first();

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $query = ContractModel::query();

        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('nr', 'like', "%{$search}%")
                    ->orWhere('currency', 'like', "%{$search}%");
            });
        }

        if (isset($filters['start_date_from'])) {
            $query->where('start_at', '>=', $filters['start_date_from']);
        }

        if (isset($filters['start_date_to'])) {
            $query->where('start_at', '<=', $filters['start_date_to']);
        }

        if (isset($filters['end_date_from'])) {
            $query->where('end_at', '>=', $filters['end_date_from']);
        }

        if (isset($filters['end_date_to'])) {
            $query->where('end_at', '<=', $filters['end_date_to']);
        }

        $models = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function save(Contract $contract): void
    {
        \DB::transaction(function () use ($contract): void {
            $model = $this->mapper->toModel($contract);
            $model->saveOrFail();
        });
    }

    public function update(Contract $contract): void
    {
        \DB::transaction(function () use ($contract): void {
            $model = ContractModel::findOrFail($contract->id());
            $model->nr = $contract->nr();
            $model->company_id = $contract->companyId();
            $model->start_at = $contract->startAt()->format('Y-m-d');
            $model->end_at = $contract->endAt()->format('Y-m-d');
            $model->amount = $contract->amount();
            $model->currency = $contract->currency();
            $model->status = $contract->status()->value;
            $model->updated_at = $contract->updatedAt();
            $model->saveOrFail();
        });
    }

    public function delete(string $id): void
    {
        \DB::transaction(function () use ($id): void {
            ContractModel::findOrFail($id)->delete();
        });
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        return \DB::transaction(function () use ($ids, $status): int {
            return ContractModel::whereIn('id', $ids)
                ->update(['status' => $status, 'updated_at' => Carbon::now()]);
        });
    }

    public function findByCompanyId(string $companyId): array
    {
        $models = ContractModel::where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function findExpired(): array
    {
        $models = ContractModel::where('status', ContractStatus::ACTIVE->value)
            ->where('end_at', '<', Carbon::now()->format('Y-m-d'))
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function findExpiring(int $days = 30): array
    {
        $expiryDate = Carbon::now()->addDays($days)->format('Y-m-d');

        $models = ContractModel::where('status', ContractStatus::ACTIVE->value)
            ->where('end_at', '<=', $expiryDate)
            ->where('end_at', '>', Carbon::now()->format('Y-m-d'))
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }
}
