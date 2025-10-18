<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'id' => $this->resource['id'],
                'name' => $this->resource['name'],
                'description' => $this->resource['description'],
                'registration_number' => $this->resource['registration_number'],
                'technical_details' => $this->resource['technical_details'],
                'created_at' => $this->resource['created_at'],
                'updated_at' => $this->resource['updated_at'],
            ],
        ];
    }
}

