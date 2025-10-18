<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'type' => 'contacts',
                'id' => $this->resource['id'],
                'attributes' => [
                    'first_name' => $this->resource['first_name'],
                    'last_name' => $this->resource['last_name'],
                    'full_name' => $this->resource['first_name'] . ' ' . $this->resource['last_name'],
                    'email' => $this->resource['email'],
                    'phone' => $this->resource['phone'],
                    'lead_level' => $this->resource['lead_level'],
                    'owner_user_id' => $this->resource['owner_user_id'],
                    'source' => $this->resource['source'],
                    'status' => $this->resource['status'],
                    'created_at' => $this->resource['created_at']?->toISOString(),
                    'updated_at' => $this->resource['updated_at']?->toISOString(),
                ],
            ],
            'meta' => [
                'request_id' => uniqid(),
            ],
        ];
    }
}
