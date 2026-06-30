<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'business_activity',
        'status',
        'linked_contract_ref',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => CustomerStatus::class,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === CustomerStatus::ACTIVE;
    }

    // Relationships
    public function customerUsers(): HasMany
    {
        return $this->hasMany(CustomerUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_users', 'customer_id', 'user_id')
                    ->withPivot('id', 'is_primary')
                    ->withTimestamps();
    }

    public function generators(): HasMany
    {
        return $this->hasMany(Generator::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function maintenanceContracts(): HasMany
    {
        return $this->hasMany(MaintenanceContract::class);
    }
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}