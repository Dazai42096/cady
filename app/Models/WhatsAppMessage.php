<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppMessage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'customer_id',
        'quotation_id',
        'rental_id',
        'maintenance_contract_id',
        'phone',
        'message_type',
        'status',
        'message_body',
        'whatsapp_url',
        'created_by',
        'opened_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
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
}