<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Dto\UsableCountDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UsableCountResource extends JsonResource
{
    public function __construct(private UsableCountDto $dto)
    {
        parent::__construct($dto);
    }

    public function toArray(Request $request): array
    {
        return [
            'entity' => $this->dto->entity(),
            'count' => $this->dto->count(),
        ];
    }
}

