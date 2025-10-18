<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\UpdateInvoiceDto;
use Carbon\Carbon;

final class UpdateInvoiceDtoFactory
{
    public function fromArray(array $data): UpdateInvoiceDto
    {
        return new UpdateInvoiceDto(
            id: $data['id'],
            number: $data['number'] ?? null,
            companyId: $data['company_id'] ?? null,
            issueDate: isset($data['issue_date']) ? Carbon::parse($data['issue_date']) : null,
            dueDate: isset($data['due_date']) ? Carbon::parse($data['due_date']) : null,
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            currency: $data['currency'] ?? null,
            status: $data['status'] ?? null,
            contractId: $data['contract_id'] ?? null
        );
    }
}
