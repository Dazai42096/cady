<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::SENT => 'تم الإرسال',
            self::ACCEPTED => 'مقبول',
            self::REJECTED => 'مرفوض',
            self::EXPIRED => 'منتهي الصلاحية',
        };
    }
}
