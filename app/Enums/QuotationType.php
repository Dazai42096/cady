<?php

namespace App\Enums;

enum QuotationType: string
{
    case SALE = 'sale';
    case RENTAL = 'rental';
    case MAINTENANCE_CONTRACT = 'maintenance_contract';
    case SPARE_PARTS = 'spare_parts';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::SALE => 'بيع مولد',
            self::RENTAL => 'تأجير مولد',
            self::MAINTENANCE_CONTRACT => 'عقد صيانة',
            self::SPARE_PARTS => 'قطع غيار',
            self::OTHER => 'أخرى',
        };
    }
}
