<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $data = $this->resource['data'] ?? $this->collection;

        return [
            'data' => collect($data)->map(function ($company) {
                // Convert Eloquent model to Domain entity if needed
                if ($company instanceof \App\Models\Company) {
                    $company = $this->convertModelToEntity($company);
                }

                return [
                    'type' => 'companies',
                    'id' => $company->id(),
                    'attributes' => [
                        'name' => $company->name(),
                        'industry' => $company->industry(),
                        'source' => $company->source()->value,
                        'status' => $company->status()->value,
                        'region' => $company->region(),
                        'vat_id' => $company->vatId(),
                        'created_by' => $company->createdBy(),
                        'created_at' => $company->createdAt()->toISOString(),
                        'updated_at' => $company->updatedAt()->toISOString(),
                    ],
                ];
            }),
            'meta' => [
                'total' => $this->resource['pagination']['total'] ?? 0,
                'per_page' => $this->resource['pagination']['per_page'] ?? 15,
                'current_page' => $this->resource['pagination']['current_page'] ?? 1,
                'last_page' => $this->resource['pagination']['last_page'] ?? 1,
                'from' => $this->resource['pagination']['from'] ?? null,
                'to' => $this->resource['pagination']['to'] ?? null,
                'request_id' => app('requestId') ?? uniqid(),
            ],
        ];
    }

    private function convertModelToEntity(\App\Models\Company $model): \App\Domain\Crm\Entity\Company
    {
        return \App\Domain\Crm\Entity\Company::create(
            name: $model->name,
            industry: $model->industry,
            source: \App\Enums\Crm\CompanySource::from($model->source),
            status: \App\Enums\Crm\CompanyStatus::from($model->status),
            region: $model->region,
            vatId: $model->vat_id,
            createdBy: (string) $model->created_by
        );
    }
}
