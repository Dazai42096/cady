<?php

namespace App\Policies;

use App\Models\Generator;
use App\Models\User;

class GeneratorPolicy
{
    /**
     * Determine whether the user can view any generators.
     * Admin, Sales, Support can view — Customers cannot.
     */
    public function viewAny(User $user): bool
    {
        return !$user->isCustomer();
    }

    /**
     * Determine whether the user can view a single generator.
     */
    public function view(User $user, Generator $generator): bool
    {
        return !$user->isCustomer();
    }

    /**
     * Determine whether the user can create generators.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSales();
    }

    /**
     * Determine whether the user can update a generator.
     */
    public function update(User $user, Generator $generator): bool
    {
        return $user->isAdmin() || $user->isSales();
    }

    /**
     * Determine whether the user can delete a generator.
     * Only admins can permanently remove generators.
     */
    public function delete(User $user, Generator $generator): bool
    {
        return $user->isAdmin();
    }
}
