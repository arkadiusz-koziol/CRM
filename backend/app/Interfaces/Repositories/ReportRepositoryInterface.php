<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Domain\Reports\Entity\Report;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReportRepositoryInterface
{
    public function findById(string $id): ?Report;

    public function findByUser(string $userId, int $perPage = 15): LengthAwarePaginator;

    public function findPublic(int $perPage = 15): LengthAwarePaginator;

    public function save(Report $report): void;

    public function delete(string $id): void;

    public function restore(string $id): void;
}
