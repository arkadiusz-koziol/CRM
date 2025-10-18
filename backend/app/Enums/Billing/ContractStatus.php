<?php

declare(strict_types=1);

namespace App\Enums\Billing;

enum ContractStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case TERMINATED = 'terminated';
}
