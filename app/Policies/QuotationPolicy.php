<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isSales() || $user->isSupport();
    }

    public function view(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin() || $user->isSales() || $user->isSupport();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSales();
    }

    public function update(User $user, Quotation $quotation): bool
    {
        return ($user->isAdmin() || $user->isSales())
            && in_array($quotation->status->value, ['draft', 'sent']);
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin() && $quotation->status->value === 'draft';
    }

    public function downloadPdf(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin() || $user->isSales() || $user->isSupport();
    }
}
