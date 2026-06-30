<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Generator;
use App\Models\MaintenanceContract;
use App\Models\Rental;
use App\Models\ServiceReport;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceReportController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request)
    {
        $reports = ServiceReport::with(['customer', 'generator', 'rental', 'maintenanceContract', 'creator'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->search);

                $q->where(function ($query) use ($search) {
                    $query->where('report_number', 'like', "%{$search}%")
                        ->orWhere('technician_name', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('company_name', 'like', "%{$search}%"))
                        ->orWhereHas('generator', fn ($g) => $g->where('serial_number', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.service-reports.index', compact('reports'));
    }

    public function create()
    {
        return view('dashboard.service-reports.create', [
            'customers' => Customer::orderBy('company_name')->get(),
            'generators' => Generator::orderBy('serial_number')->get(),
            'rentals' => Rental::with('customer')->latest()->limit(100)->get(),
            'contracts' => MaintenanceContract::with('customer')->latest()->limit(100)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $report = ServiceReport::create([
            'report_number' => $this->generateReportNumber(),
            'customer_id' => $data['customer_id'] ?? null,
            'generator_id' => $data['generator_id'] ?? null,
            'rental_id' => $data['rental_id'] ?? null,
            'maintenance_contract_id' => $data['maintenance_contract_id'] ?? null,
            'created_by' => $request->user()?->id,
            'report_type' => $data['report_type'],
            'status' => 'draft',
            'service_date' => $data['service_date'],
            'technician_name' => $data['technician_name'] ?? null,
            'fault_description' => $data['fault_description'] ?? null,
            'diagnosis' => $data['diagnosis'] ?? null,
            'mechanical_work' => $data['mechanical_work'] ?? null,
            'electrical_work' => $data['electrical_work'] ?? null,
            'spare_parts' => $data['spare_parts'] ?? null,
            'technician_notes' => $data['technician_notes'] ?? null,
            'recommended_follow_up' => $data['recommended_follow_up'] ?? null,
            'customer_visible' => $request->boolean('customer_visible'),
        ]);

        $this->log($request, 'service_report.created', $report, [], $report->toArray());

        return redirect()
            ->route('dashboard.service-reports.show', $report)
            ->with('success', 'Service report created successfully.');
    }

    public function show(ServiceReport $serviceReport)
    {
        $serviceReport->load(['customer', 'generator', 'rental', 'maintenanceContract', 'creator']);

        return view('dashboard.service-reports.show', [
            'report' => $serviceReport,
        ]);
    }

    public function submit(Request $request, ServiceReport $serviceReport)
    {
        $oldValues = $serviceReport->toArray();

        $serviceReport->update([
            'status' => 'submitted',
            'customer_visible' => true,
            'submitted_at' => now(),
        ]);

        $this->log($request, 'service_report.submitted', $serviceReport, $oldValues, $serviceReport->fresh()->toArray());

        return back()->with('success', 'Service report submitted and made visible to the customer.');
    }

    public function approve(Request $request, ServiceReport $serviceReport)
    {
        $oldValues = $serviceReport->toArray();

        $serviceReport->update([
            'status' => 'approved',
            'customer_visible' => true,
            'approved_at' => now(),
        ]);

        $this->log($request, 'service_report.approved', $serviceReport, $oldValues, $serviceReport->fresh()->toArray());

        return back()->with('success', 'Service report approved.');
    }

    public function downloadPdf(ServiceReport $serviceReport)
    {
        $serviceReport->load(['customer', 'generator', 'rental', 'maintenanceContract', 'creator']);

        return Pdf::loadView('reports.service-report-pdf', [
            'report' => $serviceReport,
        ])->download($serviceReport->report_number . '.pdf');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['nullable', 'uuid', Rule::exists('customers', 'id')],
            'generator_id' => ['nullable', 'uuid', Rule::exists('generators', 'id')],
            'rental_id' => ['nullable', 'uuid', Rule::exists('rentals', 'id')],
            'maintenance_contract_id' => ['nullable', 'uuid', Rule::exists('maintenance_contracts', 'id')],
            'report_type' => ['required', 'string', Rule::in(['maintenance', 'rental', 'emergency', 'inspection', 'support'])],
            'service_date' => ['required', 'date'],
            'technician_name' => ['nullable', 'string', 'max:255'],
            'fault_description' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'mechanical_work' => ['nullable', 'string'],
            'electrical_work' => ['nullable', 'string'],
            'spare_parts' => ['nullable', 'string'],
            'technician_notes' => ['nullable', 'string'],
            'recommended_follow_up' => ['nullable', 'string'],
            'customer_visible' => ['nullable', 'boolean'],
        ]);
    }

    private function generateReportNumber(): string
    {
        $year = now()->format('y');
        $prefix = "SR-{$year}-";

        $lastReference = ServiceReport::where('report_number', 'like', $prefix . '%')
            ->orderByDesc('report_number')
            ->value('report_number');

        $nextNumber = $lastReference ? ((int) substr($lastReference, -4)) + 1 : 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function log(Request $request, string $action, ServiceReport $report, array $oldValues, array $newValues): void
    {
        try {
            $this->auditLogService->log(
                action: $action,
                entityType: ServiceReport::class,
                entityId: $report->id,
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