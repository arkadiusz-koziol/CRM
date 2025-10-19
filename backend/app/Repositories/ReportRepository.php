<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Reports\Entity\Report;
use App\Infrastructure\Reports\ReportMapper;
use App\Interfaces\Repositories\ReportRepositoryInterface;
use App\Models\Report as ReportModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class ReportRepository implements ReportRepositoryInterface
{
    public function __construct(
        private ReportMapper $mapper
    ) {}

    public function findById(string $id): ?Report
    {
        // Validate UUID format to avoid database errors
        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
            return null;
        }

        $model = ReportModel::find($id);

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findByUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        $models = ReportModel::where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $models->getCollection()->transform(fn (ReportModel $model) => $this->mapper->toDomain($model));

        return $models;
    }

    public function findPublic(int $perPage = 15): LengthAwarePaginator
    {
        $models = ReportModel::where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $models->getCollection()->transform(fn (ReportModel $model) => $this->mapper->toDomain($model));

        return $models;
    }

    public function save(Report $report): void
    {
        DB::transaction(function () use ($report): void {
            $model = $this->mapper->toModel($report);
            $model->save();
        });
    }

    public function delete(string $id): void
    {
        DB::transaction(function () use ($id): void {
            ReportModel::where('id', $id)->delete();
        });
    }

    public function restore(string $id): void
    {
        DB::transaction(function () use ($id): void {
            ReportModel::withTrashed()->where('id', $id)->restore();
        });
    }
}
