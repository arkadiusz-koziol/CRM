<?php

declare(strict_types=1);

namespace App\Infrastructure\Billing;

use App\Domain\Billing\Entity\Invoice;
use App\Models\Invoice as InvoiceModel;
use Carbon\Carbon;

final class InvoiceMapper
{
    public function toDomain(InvoiceModel $model): Invoice
    {
        return new Invoice(
            id: $model->id,
            nr: $model->nr,
            companyId: $model->company_id,
            issueDate: Carbon::parse($model->issue_date),
            dueDate: Carbon::parse($model->due_date),
            amount: (float) $model->amount,
            currency: $model->currency,
            status: $model->status,
            createdAt: Carbon::parse($model->created_at),
            updatedAt: Carbon::parse($model->updated_at),
            contractId: $model->contract_id,
            deletedAt: $model->deleted_at ? Carbon::parse($model->deleted_at) : null
        );
    }

    public function toModel(Invoice $invoice): InvoiceModel
    {
        $model = new InvoiceModel;
        $model->id = $invoice->id();
        $model->nr = $invoice->nr();
        $model->contract_id = $invoice->contractId();
        $model->company_id = $invoice->companyId();
        $model->issue_date = $invoice->issueDate()->format('Y-m-d');
        $model->due_date = $invoice->dueDate()->format('Y-m-d');
        $model->amount = $invoice->amount();
        $model->currency = $invoice->currency();
        $model->status = $invoice->status()->value;
        $model->created_at = $invoice->createdAt();
        $model->updated_at = $invoice->updatedAt();
        $model->deleted_at = $invoice->deletedAt();

        return $model;
    }
}
