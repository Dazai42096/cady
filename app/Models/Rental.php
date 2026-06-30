<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rental extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'ref_number',
        'customer_id',
        'generator_id',
        'start_date',
        'end_date',
        'monthly_rate',
        'currency',
        'status',
        'initial_hour_meter',
        'final_hour_meter',
        'calculated_days',
        'total_amount',
        'calculation_breakdown',
        'activated_at',
        'completed_at',
        'cancelled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'monthly_rate' => 'decimal:3',
            'total_amount' => 'decimal:3',
            'calculation_breakdown' => 'array',
            'activated_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(Generator::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
    public function serviceReports(): HasMany
    {
        return $this->hasMany(ServiceReport::class);
    }
}