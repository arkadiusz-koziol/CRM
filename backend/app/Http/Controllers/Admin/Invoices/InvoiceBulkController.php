<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Invoices;

use App\Http\Requests\BulkUpdateInvoiceStatusRequest;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;

final class InvoiceBulkController
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    public function updateStatus(BulkUpdateInvoiceStatusRequest $request): JsonResponse
    {
        $updatedCount = $this->invoiceService->bulkUpdateStatus(
            $request->validated()['ids'],
            $request->validated()['status']
        );

        return response()->json([
            'message' => "Successfully updated {$updatedCount} invoices",
            'updated_count' => $updatedCount,
        ]);
    }
}
