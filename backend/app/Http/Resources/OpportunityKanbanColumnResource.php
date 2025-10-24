<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OpportunityKanbanColumnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'type' => 'kanban-columns',
                'id' => $this->resource['stage_id'],
                'attributes' => [
                    'stage_id' => $this->resource['stage_id'],
                    'stage_name' => $this->resource['stage_name'],
                    'opportunities' => OpportunityResource::collection($this->resource['opportunities']),
                    'total_opportunities' => $this->resource['opportunities']->count(),
                ],
            ],
            'meta' => [
                'request_id' => app('requestId'),
            ],
        ];
    }
}
