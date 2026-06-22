<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case SALES = 'sales';
    case SUPPORT = 'support';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'مدير النظام',
            self::SALES => 'مبيعات',
            self::SUPPORT => 'الدعم الفني',
            self::CUSTOMER => 'عميل',
        };
    }
}
