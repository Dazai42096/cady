<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class PortalRentalController extends Controller
{
    public function index(Request $request)
    {
        $customerIds = $this->customerIdsForUser($request);

        $rentals = Rental::with(['customer', 'generator'])
            ->whereIn('customer_id', $customerIds)
            ->latest()
            ->paginate(15);

        return view('portal.rentals.index', compact('rentals'));
    }

    public function show(Request $request, Rental $rental)
    {
        $rental = $this->scopedRental($request, $rental);
        $rental->load(['customer', 'generator']);

        return view('portal.rentals.show', compact('rental'));
    }

    public function export(Request $request, Rental $rental)
    {
        $rental = $this->scopedRental($request, $rental);
        $rental->load(['customer', 'generator']);

        $filename = $rental->ref_number . '-rental-breakdown.csv';
        $breakdown = $rental->calculation_breakdown['days'] ?? [];

        return response()->streamDownload(function () use ($rental, $breakdown) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Rental Reference', $rental->ref_number]);
            fputcsv($handle, ['Customer', $rental->customer?->company_name]);
            fputcsv($handle, ['Generator', $rental->generator?->serial_number]);
            fputcsv($handle, ['Start Date', $rental->start_date?->format('Y-m-d')]);
            fputcsv($handle, ['End Date', $rental->end_date?->format('Y-m-d')]);
            fputcsv($handle, ['Monthly Rate', $rental->monthly_rate]);
            fputcsv($handle, ['Currency', $rental->currency]);
            fputcsv($handle, ['Total Days', $rental->calculated_days]);
            fputcsv($handle, ['Total Amount', $rental->total_amount]);
            fputcsv($handle, []);

            fputcsv($handle, ['Date', 'Day', 'Month Days', 'Daily Rate', 'Daily Charge']);

            foreach ($breakdown as $day) {
                fputcsv($handle, [
                    $day['date'] ?? '',
                    $day['day_name'] ?? '',
                    $day['month_days'] ?? '',
                    $day['daily_rate'] ?? '',
                    $day['daily_charge'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function scopedRental(Request $request, Rental $rental): Rental
    {
        $customerIds = $this->customerIdsForUser($request);

        return Rental::with(['customer', 'generator'])
            ->whereIn('customer_id', $customerIds)
            ->whereKey($rental->id)
            ->firstOrFail();
    }

    private function customerIdsForUser(Request $request): array
    {
        return $request->user()
            ->customerUsers()
            ->pluck('customer_id')
            ->filter()
            ->values()
            ->all();
    }
}