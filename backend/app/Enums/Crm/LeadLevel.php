<?php

declare(strict_types=1);

namespace App\Enums\Crm;

enum LeadLevel: string
{
    case LEAD = 'lead';
    case CONTACT = 'contact';
}
