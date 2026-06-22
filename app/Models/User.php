<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => Role::class,
        ];
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role === Role::ADMIN;
    }

    public function isSales(): bool
    {
        return $this->role === Role::SALES;
    }

    public function isSupport(): bool
    {
        return $this->role === Role::SUPPORT;
    }

    public function isCustomer(): bool
    {
        return $this->role === Role::CUSTOMER;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [Role::ADMIN, Role::SALES, Role::SUPPORT]);
    }

    // Relationships
    public function customerUsers(): HasMany
    {
        return $this->hasMany(CustomerUser::class);
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_users', 'user_id', 'customer_id')
                    ->withPivot('id', 'is_primary')
                    ->withTimestamps();
    }
}
