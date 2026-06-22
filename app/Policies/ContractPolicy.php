<?php

namespace App\Policies;

use App\Models\MaintenanceContract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return !$user->isCustomer();
    }

    public function view(User $user, MaintenanceContract $contract): bool
    {
        return !$user->isCustomer();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSales();
    }

    public function update(User $user, MaintenanceContract $contract): bool
    {
        return ($user->isAdmin() || $user->isSales())
            && in_array($contract->status->value, ['draft', 'active']);
    }

    public function delete(User $user, MaintenanceContract $contract): bool
    {
        return $user->isAdmin() && $contract->status->value === 'draft';
    }

    public function activate(User $user, MaintenanceContract $contract): bool
    {
        return ($user->isAdmin() || $user->isSales())
            && $contract->status->value === 'draft';
    }

    public function terminate(User $user, MaintenanceContract $contract): bool
    {
        return $user->isAdmin() && $contract->status->value === 'active';
    }

    public function downloadPdf(User $user, MaintenanceContract $contract): bool
    {
        return !$user->isCustomer();
    }
}
