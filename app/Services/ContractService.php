<?php

namespace App\Services;

use App\Enums\ContractStatus;
use App\Enums\VisitStatus;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceVisit;
use Carbon\Carbon;

class ContractService
{
    /**
     * Generate a unique contract reference number.
     */
    public function generateRefNumber(): string
    {
        $year  = now()->format('Y');
        $count = MaintenanceContract::whereYear('created_at', $year)->withTrashed()->count() + 1;
        return sprintf('CON-%s-%04d', $year, $count);
    }

    /**
     * Calculate tax and total values for a contract.
     */
    public function calculateValues(float $subtotal, float $taxRate): array
    {
        $taxAmount  = round($subtotal * ($taxRate / 100), 2);
        $totalValue = round($subtotal + $taxAmount, 2);

        return [
            'tax_amount'  => $taxAmount,
            'total_value' => $totalValue,
        ];
    }

    /**
     * Activate a contract and auto-schedule visits.
     *
     * Distributes $visitCount visits evenly between start and end dates.
     */
    public function activate(MaintenanceContract $contract): void
    {
        $contract->update(['status' => ContractStatus::ACTIVE]);

        // Delete any existing visits first (re-activation edge case)
        $contract->visits()->delete();

        $startDate = Carbon::parse($contract->contract_start_date);
        $endDate   = Carbon::parse($contract->contract_end_date);

        $totalDays   = $startDate->diffInDays($endDate);
        $visitCount  = max(1, $contract->visit_count);
        $intervalDays = $totalDays > 0 ? intdiv($totalDays, $visitCount) : 30;

        for ($i = 1; $i <= $visitCount; $i++) {
            $plannedDate = $startDate->copy()->addDays($intervalDays * ($i - 1));

            // Clamp to contract end date
            if ($plannedDate->greaterThan($endDate)) {
                $plannedDate = $endDate->copy();
            }

            MaintenanceVisit::create([
                'maintenance_contract_id' => $contract->id,
                'visit_number'            => $i,
                'planned_date'            => $plannedDate->toDateString(),
                'status'                  => VisitStatus::SCHEDULED,
            ]);
        }
    }

    /**
     * Terminate a contract.
     */
    public function terminate(MaintenanceContract $contract): void
    {
        $contract->update(['status' => ContractStatus::TERMINATED]);

        // Cancel any pending scheduled visits
        $contract->visits()
            ->whereIn('status', [VisitStatus::SCHEDULED->value, VisitStatus::CONFIRMED->value])
            ->update(['status' => VisitStatus::CANCELLED]);
    }
}
