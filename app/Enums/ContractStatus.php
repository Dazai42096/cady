<?php

namespace App\Enums;

enum ContractStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case TERMINATED = 'terminated';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::SENT => 'تم الإرسال للعميل',
            self::ACTIVE => 'ساري المفعول',
            self::EXPIRED => 'منتهي الصلاحية',
            self::TERMINATED => 'ملغى / مفسوخ',
        };
    }
}
