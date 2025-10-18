<?php

declare(strict_types=1);

namespace App\Enums\Crm;

enum CompanySource: string
{
    case WEBSITE = 'website';
    case REFERRAL = 'referral';
    case SOCIAL_MEDIA = 'social_media';
    case EMAIL_CAMPAIGN = 'email_campaign';
    case COLD_CALL = 'cold_call';
    case TRADE_SHOW = 'trade_show';
    case PARTNER = 'partner';
    case OTHER = 'other';
}
