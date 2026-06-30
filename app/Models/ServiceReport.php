<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceReport extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'report_number',
        'customer_id',
        'generator_id',
        'rental_id',
        'maintenance_contract_id',
        'created_by',
        'report_type',
        'status',
        'service_date',
        'technician_name',
        'fault_description',
        'diagnosis',
        'mechanical_work',
        'electrical_work',
        'spare_parts',
        'technician_notes',
        'recommended_follow_up',
        'customer_visible',
        'submitted_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'customer_visible' => 'boolean',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
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

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function maintenanceContract(): BelongsTo
    {
        return $this->belongsTo(MaintenanceContract::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCustomerVisible(): bool
    {
        return $this->customer_visible && in_array($this->status, ['submitted', 'approved'], true);
    }
}