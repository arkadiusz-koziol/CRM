<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Crm\Entity\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function __construct(private Company $company)
    {
        parent::__construct($company);
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'type' => 'companies',
                'id' => $this->company->id(),
                'attributes' => [
                    'name' => $this->company->name(),
                    'industry' => $this->company->industry(),
                    'source' => $this->company->source()->value,
                    'status' => $this->company->status()->value,
                    'region' => $this->company->region(),
                    'vat_id' => $this->company->vatId(),
                    'created_by' => $this->company->createdBy(),
                    'created_at' => $this->company->createdAt()->toISOString(),
                    'updated_at' => $this->company->updatedAt()->toISOString(),
                ],
            ],
            'meta' => [
                'request_id' => app('requestId') ?? uniqid(),
            ],
        ];
    }
}
