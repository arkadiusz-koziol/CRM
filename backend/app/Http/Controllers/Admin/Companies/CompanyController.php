<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Companies;

use App\Factory\CreateCompanyDtoFactory;
use App\Factory\UpdateCompanyDtoFactory;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyController
{
    public function __construct(
        private CompanyService $companyService,
        private CreateCompanyDtoFactory $createDtoFactory,
        private UpdateCompanyDtoFactory $updateDtoFactory
    ) {}

    public function show(string $id): JsonResponse
    {
        $company = $this->companyService->findById($id);

        if (! $company) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '404',
                        'title' => 'Not Found',
                        'detail' => 'Company not found.',
                    ],
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => [
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
            ],
            'meta' => [
                'request_id' => uniqid(),
            ],
        ], Response::HTTP_OK);
    }

    public function store(CreateCompanyRequest $request): JsonResponse
    {
        try {
            $dto = $this->createDtoFactory->fromRequest($request);
            $company = $this->companyService->create($dto->toArray(), (string) auth()->id());

            return response()->json([
                'data' => [
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
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '400',
                        'title' => 'Bad Request',
                        'detail' => $e->getMessage(),
                    ],
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(UpdateCompanyRequest $request, string $id): JsonResponse
    {
        try {
            $dto = $this->updateDtoFactory->fromRequest($request);
            $company = $this->companyService->update($id, $dto->toArray());

            return response()->json([
                'data' => [
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
                ],
                'meta' => [
                    'request_id' => uniqid(),
                ],
            ], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '400',
                        'title' => 'Bad Request',
                        'detail' => $e->getMessage(),
                    ],
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->companyService->delete($id);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '400',
                        'title' => 'Bad Request',
                        'detail' => $e->getMessage(),
                    ],
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function convertModelToEntity(\App\Models\Company $model): \App\Domain\Crm\Entity\Company
    {
        $source = $model->source instanceof \App\Enums\Crm\CompanySource 
            ? $model->source 
            : \App\Enums\Crm\CompanySource::from($model->source);
        
        $status = $model->status instanceof \App\Enums\Crm\CompanyStatus 
            ? $model->status 
            : \App\Enums\Crm\CompanyStatus::from($model->status);

        return new \App\Domain\Crm\Entity\Company(
            id: $model->id,
            name: $model->name,
            industry: $model->industry,
            source: $source,
            status: $status,
            region: $model->region,
            vatId: $model->vat_id,
            createdBy: (string) $model->created_by,
            createdAt: \Carbon\Carbon::parse($model->created_at),
            updatedAt: \Carbon\Carbon::parse($model->updated_at),
            deletedAt: $model->deleted_at ? \Carbon\Carbon::parse($model->deleted_at) : null
        );
    }
}
