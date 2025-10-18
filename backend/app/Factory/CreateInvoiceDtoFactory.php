<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CreateInvoiceDto;
use Carbon\Carbon;

final class CreateInvoiceDtoFactory
{
    public function fromArray(array $data): CreateInvoiceDto
    {
        return new CreateInvoiceDto(
            number: $data['number'],
            companyId: $data['company_id'],
            issueDate: Carbon::parse($data['issue_date']),
            dueDate: Carbon::parse($data['due_date']),
            amount: (float) $data['amount'],
            currency: $data['currency'] ?? 'USD',
            status: $data['status'] ?? 'issued',
            contractId: $data['contract_id'] ?? null
        );
    }
}
