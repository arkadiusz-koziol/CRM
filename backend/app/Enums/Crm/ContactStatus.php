<?php

declare(strict_types=1);

namespace App\Enums\Crm;

enum ContactStatus: string
{
    case NEW = 'new';
    case ACTIVE = 'active';
    case DORMANT = 'dormant';
    case LOST = 'lost';
}
