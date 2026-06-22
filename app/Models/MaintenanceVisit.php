<?php

namespace App\Models;

use App\Enums\VisitStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceVisit extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'maintenance_contract_id',
        'visit_number',
        'planned_date',
        'confirmed_date',
        'actual_date',
        'status',
        'assigned_to',
        'technician_notes',
        'customer_notes',
    ];

    protected function casts(): array
    {
        return [
            'visit_number' => 'integer',
            'planned_date' => 'date',
            'confirmed_date' => 'date',
            'actual_date' => 'date',
            'status' => VisitStatus::class,
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(MaintenanceContract::class, 'maintenance_contract_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
