<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Reports\Entity\ReportRun;
use App\Infrastructure\Reports\ReportRunMapper;
use App\Interfaces\Repositories\ReportRunRepositoryInterface;
use App\Models\ReportRun as ReportRunModel;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class ReportRunRepository implements ReportRunRepositoryInterface
{
    public function __construct(
        private ReportRunMapper $mapper
    ) {}

    public function findById(string $id): ?ReportRun
    {
        $model = ReportRunModel::find($id);

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findByReport(string $reportId, int $perPage = 15): LengthAwarePaginator
    {
        $models = ReportRunModel::where('report_id', $reportId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $models->getCollection()->transform(fn (ReportRunModel $model) => $this->mapper->toDomain($model));

        return $models;
    }

    public function findByUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        $models = ReportRunModel::where('run_by', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $models->getCollection()->transform(fn (ReportRunModel $model) => $this->mapper->toDomain($model));

        return $models;
    }

    public function save(ReportRun $reportRun): void
    {
        DB::transaction(function () use ($reportRun): void {
            $model = $this->mapper->toModel($reportRun);
            $model->save();
        });
    }

    public function findPendingRuns(): array
    {
        $models = ReportRunModel::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return $models->map(fn (ReportRunModel $model) => $this->mapper->toDomain($model))->toArray();
    }

    public function findOldRuns(int $daysOld = 7): array
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);

        $models = ReportRunModel::where('status', 'completed')
            ->where('completed_at', '<', $cutoffDate)
            ->get();

        return $models->map(fn (ReportRunModel $model) => $this->mapper->toDomain($model))->toArray();
    }
}
