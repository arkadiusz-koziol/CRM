<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Contracts;

use App\Factory\CreateContractDtoFactory;
use App\Factory\UpdateContractDtoFactory;
use App\Http\Requests\CreateContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Http\Resources\ContractCollection;
use App\Http\Resources\ContractResource;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ContractController
{
    public function __construct(
        private ContractService $contractService,
        private CreateContractDtoFactory $createDtoFactory,
        private UpdateContractDtoFactory $updateDtoFactory
    ) {}

    public function index(Request $request): ContractCollection
    {
        $filters = $request->only([
            'company_id',
            'status',
            'search',
            'start_date_from',
            'start_date_to',
            'end_date_from',
            'end_date_to',
        ]);

        $limit = (int) $request->get('limit', 50);
        $offset = (int) $request->get('offset', 0);

        $contracts = $this->contractService->findAll($filters, $limit, $offset);

        return new ContractCollection($contracts);
    }

    public function store(CreateContractRequest $request): JsonResponse
    {
        $dto = $this->createDtoFactory->fromArray($request->validated());
        $contractId = $this->contractService->create($dto);
        $contract = $this->contractService->findById($contractId);

        return (new ContractResource($contract))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $id): ContractResource
    {
        $contract = $this->contractService->findById($id);

        if (! $contract) {
            abort(404, 'Contract not found');
        }

        return new ContractResource($contract);
    }

    public function update(UpdateContractRequest $request, string $id): ContractResource
    {
        $data = array_merge($request->validated(), ['id' => $id]);
        $dto = $this->updateDtoFactory->fromArray($data);

        $contractId = $this->contractService->update($dto);
        $contract = $this->contractService->findById($contractId);

        return new ContractResource($contract);
    }

    public function destroy(string $id): JsonResponse
    {
        $contract = $this->contractService->findById($id);

        if (! $contract) {
            return response()->json(['message' => 'Contract not found'], Response::HTTP_NOT_FOUND);
        }

        $this->contractService->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
