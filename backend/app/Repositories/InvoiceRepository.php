<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Billing\Entity\Invoice;
use App\Enums\Billing\InvoiceStatus;
use App\Infrastructure\Billing\InvoiceMapper;
use App\Interfaces\Repositories\InvoiceRepositoryInterface;
use App\Models\Invoice as InvoiceModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

final class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private InvoiceMapper $mapper
    ) {}

    public function findById(string $id): ?Invoice
    {
        // Validate UUID format before querying database
        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
            return null;
        }

        $model = InvoiceModel::find($id);

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findByNumber(string $number): ?Invoice
    {
        $model = InvoiceModel::where('nr', $number)->first();

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $query = InvoiceModel::query();

        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (isset($filters['contract_id'])) {
            $query->where('contract_id', $filters['contract_id']);
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

        if (isset($filters['issue_date_from'])) {
            $query->where('issue_date', '>=', $filters['issue_date_from']);
        }

        if (isset($filters['issue_date_to'])) {
            $query->where('issue_date', '<=', $filters['issue_date_to']);
        }

        if (isset($filters['due_date_from'])) {
            $query->where('due_date', '>=', $filters['due_date_from']);
        }

        if (isset($filters['due_date_to'])) {
            $query->where('due_date', '<=', $filters['due_date_to']);
        }

        $models = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function save(Invoice $invoice): void
    {
        \DB::transaction(function () use ($invoice): void {
            $model = $this->mapper->toModel($invoice);
            $model->saveOrFail();
        });
    }

    public function update(Invoice $invoice): void
    {
        \DB::transaction(function () use ($invoice): void {
            $model = InvoiceModel::findOrFail($invoice->id());
            $model->nr = $invoice->nr();
            $model->contract_id = $invoice->contractId();
            $model->company_id = $invoice->companyId();
            $model->issue_date = $invoice->issueDate()->format('Y-m-d');
            $model->due_date = $invoice->dueDate()->format('Y-m-d');
            $model->amount = $invoice->amount();
            $model->currency = $invoice->currency();
            $model->status = $invoice->status()->value;
            $model->updated_at = $invoice->updatedAt();
            $model->saveOrFail();
        });
    }

    public function delete(string $id): void
    {
        \DB::transaction(function () use ($id): void {
            InvoiceModel::findOrFail($id)->delete();
        });
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        return \DB::transaction(function () use ($ids, $status): int {
            return InvoiceModel::whereIn('id', $ids)
                ->update(['status' => $status, 'updated_at' => Carbon::now()]);
        });
    }

    public function findByCompanyId(string $companyId): array
    {
        $models = InvoiceModel::where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function findByContractId(string $contractId): array
    {
        $models = InvoiceModel::where('contract_id', $contractId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function findOverdue(): array
    {
        $models = InvoiceModel::where('status', InvoiceStatus::ISSUED->value)
            ->where('due_date', '<', Carbon::now()->format('Y-m-d'))
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }

    public function findDueSoon(int $days = 7): array
    {
        $dueDate = Carbon::now()->addDays($days)->format('Y-m-d');

        $models = InvoiceModel::where('status', InvoiceStatus::ISSUED->value)
            ->where('due_date', '<=', $dueDate)
            ->where('due_date', '>', Carbon::now()->format('Y-m-d'))
            ->get();

        return $models->map(fn ($model) => $this->mapper->toDomain($model))->toArray();
    }
}
