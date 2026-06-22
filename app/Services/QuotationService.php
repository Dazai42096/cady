<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Support\Str;

class QuotationService
{
    /**
     * Generate a unique reference number for a quotation.
     */
    public function generateRefNumber(): string
    {
        $year  = now()->format('Y');
        $count = Quotation::whereYear('created_at', $year)->withTrashed()->count() + 1;
        return sprintf('QUO-%s-%04d', $year, $count);
    }

    /**
     * Sync quotation items from submitted form data.
     * Deletes removed items, creates new ones.
     */
    public function syncItems(Quotation $quotation, array $itemsData): void
    {
        $quotation->items()->delete();

        foreach ($itemsData as $index => $item) {
            $qty       = (float) ($item['qty'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $total     = round($qty * $unitPrice, 2);

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description'  => $item['description'],
                'qty'          => $qty,
                'unit_price'   => $unitPrice,
                'total'        => $total,
                'sort_order'   => $index + 1,
            ]);
        }

        $quotation->recalculateTotals();
    }
}
