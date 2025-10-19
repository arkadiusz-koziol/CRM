<?php

declare(strict_types=1);

namespace App\Http\Resources\Export;

use Illuminate\Http\Resources\Json\JsonResource;

final class ExportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'data' => [
                'type' => 'exports',
                'id' => $this->resource['id'],
                'attributes' => [
                    'filename' => $this->resource['filename'],
                    'format' => $this->resource['format'],
                    'created_at' => $this->resource['created_at'],
                    'expires_at' => $this->resource['expires_at'],
                ],
            ],
            'meta' => [
                'request_id' => app('requestId'),
            ],
        ];
    }
}
