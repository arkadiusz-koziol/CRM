<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Reports\Entity\Report;
use Illuminate\Http\Resources\Json\JsonResource;

final class ReportResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Report $report */
        $report = $this->resource;

        return [
            'id' => $report->id(),
            'name' => $report->name(),
            'description' => $report->description(),
            'source' => $report->source(),
            'columns' => $report->columns(),
            'filters' => $report->filters(),
            'sorting' => $report->sorting(),
            'created_by' => $report->createdBy(),
            'is_public' => $report->isPublic(),
            'created_at' => $report->createdAt()->toIso8601String(),
            'updated_at' => $report->updatedAt()->toIso8601String(),
            'deleted_at' => $report->deletedAt()?->toIso8601String(),
        ];
    }
}
