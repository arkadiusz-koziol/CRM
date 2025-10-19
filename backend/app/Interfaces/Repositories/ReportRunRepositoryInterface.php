<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Reports\Entity\ReportRun;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReportRunRepositoryInterface
{
    public function findById(string $id): ?ReportRun;

    public function findByReport(string $reportId, int $perPage = 15): LengthAwarePaginator;

    public function findByUser(string $userId, int $perPage = 15): LengthAwarePaginator;

    public function save(ReportRun $reportRun): void;

    public function findPendingRuns(): array;

    public function findOldRuns(int $daysOld = 7): array;
}
