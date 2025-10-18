<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Invoices;

use App\Factory\CreateInvoiceDtoFactory;
use App\Factory\UpdateInvoiceDtoFactory;
use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class InvoiceController
{
    public function __construct(
        private InvoiceService $invoiceService,
        private CreateInvoiceDtoFactory $createDtoFactory,
        private UpdateInvoiceDtoFactory $updateDtoFactory
    ) {}

    public function index(Request $request): InvoiceCollection
    {
        $filters = $request->only([
            'company_id',
            'contract_id',
            'status',
            'search',
            'issue_date_from',
            'issue_date_to',
            'due_date_from',
            'due_date_to',
        ]);

        $limit = (int) $request->get('limit', 50);
        $offset = (int) $request->get('offset', 0);

        $invoices = $this->invoiceService->findAll($filters, $limit, $offset);

        return new InvoiceCollection($invoices);
    }

    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        $dto = $this->createDtoFactory->fromArray($request->validated());
        $invoiceId = $this->invoiceService->create($dto);
        $invoice = $this->invoiceService->findById($invoiceId);

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $id): InvoiceResource
    {
        $invoice = $this->invoiceService->findById($id);

        if (! $invoice) {
            abort(404, 'Invoice not found');
        }

        return new InvoiceResource($invoice);
    }

    public function update(UpdateInvoiceRequest $request, string $id): InvoiceResource
    {
        $data = array_merge($request->validated(), ['id' => $id]);
        $dto = $this->updateDtoFactory->fromArray($data);

        $invoiceId = $this->invoiceService->update($dto);
        $invoice = $this->invoiceService->findById($invoiceId);

        return new InvoiceResource($invoice);
    }

    public function destroy(string $id): JsonResponse
    {
        $invoice = $this->invoiceService->findById($id);

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        $this->invoiceService->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
