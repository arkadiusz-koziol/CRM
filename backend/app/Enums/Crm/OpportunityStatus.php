<?php

declare(strict_types=1);

namespace App\Enums\Crm;

enum OpportunityStatus: string
{
    case OPEN = 'open';
    case WON = 'won';
    case LOST = 'lost';
}
