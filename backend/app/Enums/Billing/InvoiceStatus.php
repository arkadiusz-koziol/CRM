<?php

declare(strict_types=1);

namespace App\Enums\Billing;

enum InvoiceStatus: string
{
    case ISSUED = 'issued';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
}
