<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use App\Enums\QuotationType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'customer_id',
        'created_by',
        'ref_number',
        'type',
        'status',
        'quotation_date',
        'valid_until',
        'project',
        'subtotal',
        'discount',
        'tax_rate',
        'tax_amount',
        'total',
        'currency',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuotationStatus::class,
            'type' => QuotationType::class,
            'quotation_date' => 'date',
            'valid_until' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Recalculates quotation subtotal, tax_amount and total based on its items.
     */
    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('total');
        
        $discount = $this->discount ?? 0.00;
        $taxRate = $this->tax_rate ?? 0.00;
        
        // Calculate tax amount on subtotal after discount
        $taxable = max(0, $subtotal - $discount);
        $taxAmount = $taxable * ($taxRate / 100);
        $total = $taxable + $taxAmount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);
    }

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }
}
