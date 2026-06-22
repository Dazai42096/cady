<?php

namespace App\Models;

use App\Enums\GeneratorStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Generator extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'customer_id',
        'serial_number',
        'model',
        'brand',
        'capacity_kva',
        'fuel_type',
        'location',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'capacity_kva' => 'decimal:2',
            'status' => GeneratorStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function maintenanceContracts(): HasMany
    {
        return $this->hasMany(MaintenanceContract::class);
    }
}
