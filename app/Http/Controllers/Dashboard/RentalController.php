<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\GeneratorStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Generator;
use App\Models\Rental;
use App\Services\AuditLogService;
use App\Services\RentalCalculationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RentalController extends Controller
{
    public function __construct(
        private readonly RentalCalculationService $rentalCalculationService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request)
    {
        $rentals = Rental::with(['customer', 'generator'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->search);

                $q->where(function ($query) use ($search) {
                    $query->where('ref_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('company_name', 'like', "%{$search}%"))
                        ->orWhereHas('generator', fn ($g) => $g->where('serial_number', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.rentals.index', compact('rentals'));
    }

    public function create()
    {
        $customers = Customer::orderBy('company_name')->get();

        $generators = Generator::where('status', GeneratorStatus::AVAILABLE)
            ->orderBy('serial_number')
            ->get();

        return view('dashboard.rentals.create', compact('customers', 'generators'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedRentalData($request);

        if (!$this->generatorIsAvailable($data['generator_id'], $data['start_date'], $data['end_date'])) {
            return back()
                ->withInput()
                ->withErrors(['generator_id' => 'This generator is not available for the selected rental period.']);
        }

        $calculation = $this->rentalCalculationService->calculate(
            $data['start_date'],
            $data['end_date'],
            (float) $data['monthly_rate']
        );

        $rental = Rental::create([
            'ref_number' => $this->generateReferenceNumber(),
            'customer_id' => $data['customer_id'],
            'generator_id' => $data['generator_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'monthly_rate' => $data['monthly_rate'],
            'currency' => $data['currency'],
            'status' => 'draft',
            'initial_hour_meter' => $data['initial_hour_meter'] ?? 0,
            'calculated_days' => $calculation['days_count'],
            'total_amount' => $calculation['total_amount'],
            'calculation_breakdown' => $calculation,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->log($request, 'rental.created', $rental, [], $rental->toArray());

        return redirect()
            ->route('dashboard.rentals.show', $rental)
            ->with('success', 'Rental created successfully.');
    }

    public function show(Rental $rental)
    {
        $rental->load(['customer', 'generator']);

        return view('dashboard.rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        if (in_array($rental->status, ['completed', 'cancelled'], true)) {
            return redirect()
                ->route('dashboard.rentals.show', $rental)
                ->withErrors(['rental' => 'Completed or cancelled rentals cannot be edited.']);
        }

        $customers = Customer::orderBy('company_name')->get();

        $generators = Generator::where(function ($query) use ($rental) {
                $query->where('status', GeneratorStatus::AVAILABLE)
                    ->orWhere('id', $rental->generator_id);
            })
            ->orderBy('serial_number')
            ->get();

        return view('dashboard.rentals.edit', compact('rental', 'customers', 'generators'));
    }

    public function update(Request $request, Rental $rental)
    {
        if (in_array($rental->status, ['completed', 'cancelled'], true)) {
            return back()->withErrors(['rental' => 'Completed or cancelled rentals cannot be updated.']);
        }

        $oldValues = $rental->toArray();
        $data = $this->validatedRentalData($request);

        if (!$this->generatorIsAvailable($data['generator_id'], $data['start_date'], $data['end_date'], $rental->id)) {
            return back()
                ->withInput()
                ->withErrors(['generator_id' => 'This generator is not available for the selected rental period.']);
        }

        $calculation = $this->rentalCalculationService->calculate(
            $data['start_date'],
            $data['end_date'],
            (float) $data['monthly_rate']
        );

        $rental->update([
            'customer_id' => $data['customer_id'],
            'generator_id' => $data['generator_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'monthly_rate' => $data['monthly_rate'],
            'currency' => $data['currency'],
            'initial_hour_meter' => $data['initial_hour_meter'] ?? 0,
            'calculated_days' => $calculation['days_count'],
            'total_amount' => $calculation['total_amount'],
            'calculation_breakdown' => $calculation,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->log($request, 'rental.updated', $rental, $oldValues, $rental->fresh()->toArray());

        return redirect()
            ->route('dashboard.rentals.show', $rental)
            ->with('success', 'Rental updated successfully.');
    }

    public function activate(Request $request, Rental $rental)
    {
        if (!$rental->isDraft()) {
            return back()->withErrors(['rental' => 'Only draft rentals can be activated.']);
        }

        if (!$this->generatorIsAvailable($rental->generator_id, $rental->start_date->toDateString(), $rental->end_date->toDateString(), $rental->id)) {
            return back()->withErrors(['generator_id' => 'This generator is no longer available for this rental period.']);
        }

        $oldValues = $rental->toArray();

        $rental->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);

        $rental->generator->update([
            'status' => GeneratorStatus::RENTED,
            'customer_id' => $rental->customer_id,
        ]);

        $this->log($request, 'rental.activated', $rental, $oldValues, $rental->fresh()->toArray());

        return back()->with('success', 'Rental activated successfully.');
    }

    public function extend(Request $request, Rental $rental)
    {
        if (!in_array($rental->status, ['draft', 'active'], true)) {
            return back()->withErrors(['rental' => 'Only draft or active rentals can be extended.']);
        }

        $request->validate([
            'end_date' => ['required', 'date', 'after_or_equal:' . $rental->start_date->toDateString()],
        ]);

        if (!$this->generatorIsAvailable($rental->generator_id, $rental->start_date->toDateString(), $request->end_date, $rental->id)) {
            return back()->withErrors(['end_date' => 'This extension conflicts with another rental for the same generator.']);
        }

        $oldValues = $rental->toArray();

        $calculation = $this->rentalCalculationService->calculate(
            $rental->start_date->toDateString(),
            $request->end_date,
            (float) $rental->monthly_rate
        );

        $rental->update([
            'end_date' => $request->end_date,
            'calculated_days' => $calculation['days_count'],
            'total_amount' => $calculation['total_amount'],
            'calculation_breakdown' => $calculation,
        ]);

        $this->log($request, 'rental.extended', $rental, $oldValues, $rental->fresh()->toArray());

        return back()->with('success', 'Rental extended successfully.');
    }

    public function complete(Request $request, Rental $rental)
    {
        if (!$rental->isActive()) {
            return back()->withErrors(['rental' => 'Only active rentals can be completed.']);
        }

        $request->validate([
            'final_hour_meter' => ['required', 'integer', 'min:' . (int) $rental->initial_hour_meter],
        ]);

        $oldValues = $rental->toArray();

        $rental->update([
            'status' => 'completed',
            'final_hour_meter' => $request->integer('final_hour_meter'),
            'completed_at' => now(),
        ]);

        $rental->generator->update([
            'status' => GeneratorStatus::AVAILABLE,
            'customer_id' => null,
        ]);

        $this->log($request, 'rental.completed', $rental, $oldValues, $rental->fresh()->toArray());

        return back()->with('success', 'Rental completed successfully.');
    }

    public function cancel(Request $request, Rental $rental)
    {
        if (in_array($rental->status, ['completed', 'cancelled'], true)) {
            return back()->withErrors(['rental' => 'This rental cannot be cancelled.']);
        }

        $oldValues = $rental->toArray();

        $rental->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        if ($rental->generator && $rental->generator->status === GeneratorStatus::RENTED) {
            $rental->generator->update([
                'status' => GeneratorStatus::AVAILABLE,
                'customer_id' => null,
            ]);
        }

        $this->log($request, 'rental.cancelled', $rental, $oldValues, $rental->fresh()->toArray());

        return back()->with('success', 'Rental cancelled successfully.');
    }

    public function updateHourMeter(Request $request, Rental $rental)
    {
        $request->validate([
            'final_hour_meter' => ['nullable', 'integer', 'min:' . (int) $rental->initial_hour_meter],
        ]);

        $oldValues = $rental->toArray();

        $rental->update([
            'final_hour_meter' => $request->filled('final_hour_meter') ? $request->integer('final_hour_meter') : null,
        ]);

        $this->log($request, 'rental.hour_meter_updated', $rental, $oldValues, $rental->fresh()->toArray());

        return back()->with('success', 'Hour meter updated successfully.');
    }

    public function export(Rental $rental)
    {
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

    private function validatedRentalData(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'uuid', Rule::exists('customers', 'id')],
            'generator_id' => ['required', 'uuid', Rule::exists('generators', 'id')],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'monthly_rate' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'initial_hour_meter' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function generatorIsAvailable(string $generatorId, string $startDate, string $endDate, ?string $excludeRentalId = null): bool
    {
        $generator = Generator::find($generatorId);

        if (!$generator) {
            return false;
        }

        if ($generator->status !== GeneratorStatus::AVAILABLE && !$excludeRentalId) {
            return false;
        }

        $query = Rental::query()
            ->where('generator_id', $generatorId)
            ->whereIn('status', ['draft', 'active'])
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate);

        if ($excludeRentalId) {
            $query->where('id', '!=', $excludeRentalId);
        }

        return !$query->exists();
    }

    private function generateReferenceNumber(): string
    {
        $year = now()->format('y');
        $prefix = "R-{$year}-";

        $lastReference = Rental::where('ref_number', 'like', $prefix . '%')
            ->orderByDesc('ref_number')
            ->value('ref_number');

        $nextNumber = 1;

        if ($lastReference) {
            $nextNumber = ((int) substr($lastReference, -4)) + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function log(Request $request, string $action, Rental $rental, array $oldValues, array $newValues): void
    {
        try {
            $this->auditLogService->log(
                action: $action,
                entityType: Rental::class,
                entityId: $rental->id,
                oldValues: $oldValues,
                newValues: array_merge($newValues, [
                    'admin_email' => $request->user()?->email,
                    'ip' => $request->ip(),
                ])
            );
        } catch (\Throwable) {
            //
        }
    }
}