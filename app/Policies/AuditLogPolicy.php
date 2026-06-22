<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * Determine whether the user can view any audit logs.
     * Restricted to Admin only.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view a single audit log.
     * Restricted to Admin only.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->isAdmin();
    }

    /**
     * Audit logs are read-only; no creation from UI.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AuditLog $auditLog): bool
    {
        return false;
    }

    public function delete(User $user, AuditLog $auditLog): bool
    {
        return false;
    }
}
