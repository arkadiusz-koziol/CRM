<?php

declare(strict_types=1);

namespace App\Services\Reports;

use App\Domain\Reports\Entity\Report;
use App\Domain\Reports\Entity\ReportRun;
use App\Enums\Reports\ReportSource;
use App\Interfaces\Repositories\ReportRepositoryInterface;
use Psr\Log\LoggerInterface;

final class ReportGeneratorService
{
    public function __construct(
        private ReportRepositoryInterface $reportRepository,
        private LoggerInterface $logger
    ) {}

    public function generateReport(ReportRun $reportRun): string
    {
        $report = $this->reportRepository->findById($reportRun->reportId());

        if (! $report) {
            throw new \RuntimeException("Report with ID {$reportRun->reportId()} not found.");
        }

        $this->logger->info('Generating report', [
            'report_id' => $report->id(),
            'report_run_id' => $reportRun->id(),
            'source' => $report->source(),
        ]);

        $data = $this->fetchData($report);
        $filePath = $this->generateCsvFile($report, $data);

        return $filePath;
    }

    private function fetchData(Report $report): array
    {
        $query = $this->buildQuery($report);
        $data = $query->get();

        return $data->toArray();
    }

    private function buildQuery(Report $report): \Illuminate\Database\Eloquent\Builder
    {
        $source = ReportSource::from($report->source());
        $model = $this->getModelForSource($source);

        $query = $model->select($report->columns());

        if ($report->filters()) {
            $this->applyFilters($query, $report->filters());
        }

        if ($report->sorting()) {
            $this->applySorting($query, $report->sorting());
        }

        return $query;
    }

    private function getModelForSource(ReportSource $source): \Illuminate\Database\Eloquent\Model
    {
        return match ($source) {
            ReportSource::USERS => new \App\Models\User,
            ReportSource::COMPANIES => new \App\Models\Company,
            ReportSource::CONTACTS => new \App\Models\Contact,
            ReportSource::TASKS => new \App\Models\Task,
            ReportSource::OPPORTUNITIES => new \App\Models\Opportunity,
            ReportSource::INVOICES => new \App\Models\Invoice,
        };
    }

    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        foreach ($filters as $filter) {
            $field = $filter['field'];
            $operator = $filter['operator'];
            $value = $filter['value'];

            match ($operator) {
                'eq' => $query->where($field, '=', $value),
                'ne' => $query->where($field, '!=', $value),
                'gt' => $query->where($field, '>', $value),
                'lt' => $query->where($field, '<', $value),
                'gte' => $query->where($field, '>=', $value),
                'lte' => $query->where($field, '<=', $value),
                'in' => $query->whereIn($field, $value),
                'not_in' => $query->whereNotIn($field, $value),
                'like' => $query->where($field, 'LIKE', $value),
                'between' => $query->whereBetween($field, $value),
                'is_null' => $query->whereNull($field),
                'is_not_null' => $query->whereNotNull($field),
                default => throw new \InvalidArgumentException("Unknown operator: {$operator}"),
            };
        }
    }

    private function applySorting(\Illuminate\Database\Eloquent\Builder $query, array $sorting): void
    {
        foreach ($sorting as $sort) {
            $field = $sort['field'];
            $direction = $sort['direction'] ?? 'asc';

            $query->orderBy($field, $direction);
        }
    }

    private function generateCsvFile(Report $report, array $data): string
    {
        $fileName = 'reports/'.uniqid().'.csv';
        $filePath = storage_path('app/'.$fileName);

        $file = fopen($filePath, 'w');

        if (! $file) {
            throw new \RuntimeException('Could not create CSV file.');
        }

        try {
            // Write headers
            fputcsv($file, $report->columns());

            // Write data
            foreach ($data as $row) {
                $csvRow = [];
                foreach ($report->columns() as $column) {
                    $csvRow[] = $row[$column] ?? '';
                }
                fputcsv($file, $csvRow);
            }
        } finally {
            fclose($file);
        }

        return $fileName;
    }
}
