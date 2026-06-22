<?php

namespace App\Enums;

enum VisitStatus: string
{
    case SCHEDULED = 'scheduled';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::SCHEDULED => 'مجدولة',
            self::CONFIRMED => 'مؤكدة',
            self::IN_PROGRESS => 'قيد العمل',
            self::COMPLETED => 'مكتملة',
            self::CANCELLED => 'ملغاة',
        };
    }
}
