<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;

class QuotationService
{
    public function generateRefNumber(): string
    {
        $year = now()->format('Y');
        $count = Quotation::whereYear('created_at', $year)->withTrashed()->count() + 1;

        return sprintf('Q-%s-%04d', $year, $count);
    }

    public function syncItems(Quotation $quotation, array $itemsData): void
    {
        $quotation->items()->delete();

        foreach ($itemsData as $index => $item) {
            $quantity = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $total = round($quantity * $unitPrice, 2);

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description'  => $item['description'],
                'quantity'     => $quantity,
                'unit_price'   => $unitPrice,
                'total'        => $total,
                'sort_order'   => $index + 1,
            ]);
        }

        $quotation->recalculateTotals();
    }
}