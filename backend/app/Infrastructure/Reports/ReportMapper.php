<?php

declare(strict_types=1);

namespace App\Infrastructure\Reports;

use App\Domain\Reports\Entity\Report;
use App\Models\Report as ReportModel;
use Carbon\Carbon;

final class ReportMapper
{
    public function toDomain(ReportModel $model): Report
    {
        return Report::reconstitute(
            $model->id,
            $model->name,
            $model->description,
            $model->source->value,
            $model->columns,
            $model->filters,
            $model->sorting,
            (string) $model->created_by,
            $model->is_public,
            Carbon::parse($model->created_at),
            Carbon::parse($model->updated_at),
            $model->deleted_at ? Carbon::parse($model->deleted_at) : null,
        );
    }

    public function toModel(Report $report): ReportModel
    {
        $model = new ReportModel;
        $model->id = $report->id();
        $model->name = $report->name();
        $model->description = $report->description();
        $model->source = $report->source();
        $model->columns = $report->columns();
        $model->filters = $report->filters();
        $model->sorting = $report->sorting();
        $model->created_by = $report->createdBy();
        $model->is_public = $report->isPublic();
        $model->created_at = $report->createdAt();
        $model->updated_at = $report->updatedAt();
        $model->deleted_at = $report->deletedAt();

        return $model;
    }
}
