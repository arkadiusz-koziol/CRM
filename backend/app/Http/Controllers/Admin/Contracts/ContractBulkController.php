<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Contracts;

use App\Http\Requests\BulkUpdateContractStatusRequest;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;

final class ContractBulkController
{
    public function __construct(
        private ContractService $contractService
    ) {}

    public function updateStatus(BulkUpdateContractStatusRequest $request): JsonResponse
    {
        $updatedCount = $this->contractService->bulkUpdateStatus(
            $request->validated()['ids'],
            $request->validated()['status']
        );

        return response()->json([
            'message' => "Successfully updated {$updatedCount} contracts",
            'updated_count' => $updatedCount,
        ]);
    }
}
