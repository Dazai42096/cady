<?php

namespace App\Enums;

enum GeneratorStatus: string
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case MAINTENANCE = 'maintenance';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'متوفر',
            self::RENTED => 'مؤجر',
            self::MAINTENANCE => 'تحت الصيانة',
            self::INACTIVE => 'خارج الخدمة',
        };
    }
}
