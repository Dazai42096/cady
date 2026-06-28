<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Role helpers.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSales(): bool
    {
        return $this->role === 'sales';
    }

    public function isSupport(): bool
    {
        return $this->role === 'support';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'sales', 'support'], true);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->role, $roles, true);
    }

    /**
     * Customer-user links.
     * Used for connecting customer portal users to customer records.
     */
    public function customerUsers(): HasMany
    {
        return $this->hasMany(CustomerUser::class);
    }

    /**
     * Audit logs created by this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}