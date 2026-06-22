<?php

namespace App\Policies;

use App\Models\MaintenanceVisit;
use App\Models\User;

class VisitPolicy
{
    public function viewAny(User $user): bool
    {
        return !$user->isCustomer();
    }

    public function view(User $user, MaintenanceVisit $visit): bool
    {
        return !$user->isCustomer();
    }

    public function update(User $user, MaintenanceVisit $visit): bool
    {
        return $user->isAdmin() || $user->isSupport();
    }

    public function updateStatus(User $user, MaintenanceVisit $visit): bool
    {
        return $user->isAdmin() || $user->isSupport();
    }
}
