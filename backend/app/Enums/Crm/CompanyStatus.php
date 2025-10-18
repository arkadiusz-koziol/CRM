<?php

declare(strict_types=1);

namespace App\Enums\Crm;

enum CompanyStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PROSPECT = 'prospect';
}
