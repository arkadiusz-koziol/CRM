<?php

declare(strict_types=1);

namespace App\Services\Reports;

use App\Domain\Reports\Entity\Report;
use App\Enums\Reports\ReportSource;
use App\Interfaces\Repositories\ReportRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class ReportService
{
    public function __construct(
        private ReportRepositoryInterface $reportRepository,
        private LoggerInterface $logger
    ) {}

    public function create(
        string $name,
        ?string $description,
        string $source,
        array $columns,
        ?array $filters,
        ?array $sorting,
        string $createdBy,
        bool $isPublic = false,
    ): string {
        $this->validateSource($source);
        $this->validateColumns($source, $columns);

        $report = Report::create(
            Str::uuid()->toString(),
            $name,
            $description,
            $source,
            $columns,
            $filters,
            $sorting,
            $createdBy,
            $isPublic,
        );

        $this->reportRepository->save($report);

        $this->logger->info('Report created', [
            'report_id' => $report->id(),
            'name' => $report->name(),
            'source' => $report->source(),
            'created_by' => $report->createdBy(),
        ]);

        return $report->id();
    }

    public function update(
        string $id,
        string $name,
        ?string $description,
        array $columns,
        ?array $filters,
        ?array $sorting,
        bool $isPublic,
    ): void {
        $report = $this->reportRepository->findById($id);

        if (! $report) {
            throw new \RuntimeException("Report with ID {$id} not found.");
        }

        $this->validateColumns($report->source(), $columns);

        $report->updateName($name);
        $report->updateDescription($description);
        $report->updateColumns($columns);
        $report->updateFilters($filters);
        $report->updateSorting($sorting);
        $report->updateVisibility($isPublic);

        $this->reportRepository->save($report);

        $this->logger->info('Report updated', [
            'report_id' => $report->id(),
            'name' => $report->name(),
        ]);
    }

    public function delete(string $id): void
    {
        $report = $this->reportRepository->findById($id);

        if (! $report) {
            throw new \RuntimeException("Report with ID {$id} not found.");
        }

        $report->delete();
        $this->reportRepository->save($report);

        $this->logger->info('Report deleted', [
            'report_id' => $report->id(),
        ]);
    }

    public function restore(string $id): void
    {
        $report = $this->reportRepository->findById($id);

        if (! $report) {
            throw new \RuntimeException("Report with ID {$id} not found.");
        }

        $report->restore();
        $this->reportRepository->save($report);

        $this->logger->info('Report restored', [
            'report_id' => $report->id(),
        ]);
    }

    public function findById(string $id): ?Report
    {
        return $this->reportRepository->findById($id);
    }

    public function findByUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->reportRepository->findByUser($userId, $perPage);
    }

    public function findPublic(int $perPage = 15): LengthAwarePaginator
    {
        return $this->reportRepository->findPublic($perPage);
    }

    private function validateSource(string $source): void
    {
        if (! ReportSource::tryFrom($source)) {
            throw new \InvalidArgumentException("Invalid report source: {$source}");
        }
    }

    private function validateColumns(string $source, array $columns): void
    {
        $reportSource = ReportSource::from($source);
        $allowedColumns = $reportSource->getAllowedColumns();

        foreach ($columns as $column) {
            if (! in_array($column, $allowedColumns, true)) {
                throw new \InvalidArgumentException("Column '{$column}' is not allowed for source '{$source}'");
            }
        }
    }
}
