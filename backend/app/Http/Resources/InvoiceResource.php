<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Billing\Entity\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class InvoiceResource extends JsonResource
{
    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice);
    }

    public function toArray(Request $request): array
    {
        /** @var Invoice $invoice */
        $invoice = $this->resource;

        return [
            'type' => 'invoices',
            'id' => $invoice->id(),
            'attributes' => [
                'number' => $invoice->nr(),
                'company_id' => $invoice->companyId(),
                'contract_id' => $invoice->contractId(),
                'issue_date' => $invoice->issueDate()->format('Y-m-d'),
                'due_date' => $invoice->dueDate()->format('Y-m-d'),
                'amount' => $invoice->amount(),
                'currency' => $invoice->currency(),
                'status' => $invoice->status()->value,
                'created_at' => $invoice->createdAt()->toISOString(),
                'updated_at' => $invoice->updatedAt()->toISOString(),
            ],
        ];
    }
}
