<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CreateContractDto;
use Carbon\Carbon;

final class CreateContractDtoFactory
{
    public function fromArray(array $data): CreateContractDto
    {
        return new CreateContractDto(
            number: $data['number'],
            companyId: $data['company_id'],
            startAt: Carbon::parse($data['start_at']),
            endAt: Carbon::parse($data['end_at']),
            amount: (float) $data['amount'],
            currency: $data['currency'] ?? 'USD',
            status: $data['status'] ?? 'draft'
        );
    }
}
