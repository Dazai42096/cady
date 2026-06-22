<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceContract extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'customer_id',
        'generator_id',
        'created_by',
        'ref_number',
        'to_name',
        'project',
        'status',
        'contract_start_date',
        'contract_end_date',
        'visit_count',
        'payment_method',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_value',
        'currency',
        'terms',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContractStatus::class,
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'visit_count' => 'integer',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_value' => 'decimal:2',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === ContractStatus::ACTIVE;
    }

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(Generator::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(MaintenanceVisit::class);
    }
}
