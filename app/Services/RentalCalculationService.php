<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RentalCalculationService
{
    /**
     * Master Spec rule:
     * - Start and end dates are inclusive.
     * - Friday and Saturday are included.
     * - Daily rate uses the actual number of days in that month.
     * - Multi-month rentals are split by month and summed.
     */
    public function calculate(string $startDate, string $endDate, float $monthlyRate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($end->lt($start)) {
            throw new \InvalidArgumentException('End date must be after or equal to start date.');
        }

        $period = CarbonPeriod::create($start, $end);

        $days = [];
        $total = 0.0;

        foreach ($period as $date) {
            $daysInMonth = $date->daysInMonth;
            $dailyRate = $monthlyRate / $daysInMonth;
            $dailyCharge = $dailyRate;

            $days[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->format('l'),
                'month_days' => $daysInMonth,
                'daily_rate' => round($dailyRate, 3),
                'daily_charge' => round($dailyCharge, 3),
            ];

            $total += $dailyCharge;
        }

        return [
            'days_count' => count($days),
            'total_amount' => round($total, 3),
            'days' => $days,
        ];
    }
}