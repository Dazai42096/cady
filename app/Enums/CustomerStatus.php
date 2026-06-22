<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case PENDING_ADMIN_LINK = 'pending_admin_link';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::PENDING_ADMIN_LINK => 'قيد الانتظار لموافقة الإدارة',
            self::ACTIVE => 'نشط',
            self::SUSPENDED => 'موقوف مؤقتاً',
            self::INACTIVE => 'غير نشط',
        };
    }
}
