<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ResetPasswordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'type' => 'reset-password',
                'attributes' => [
                    'message' => $this->resource['message'],
                ],
            ],
            'meta' => ['timestamp' => now()->toISOString()],
        ];
    }
}
