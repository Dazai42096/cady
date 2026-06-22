<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ContractStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Customer;
use App\Models\Generator;
use App\Models\MaintenanceContract;
use App\Services\AuditLogService;
use App\Services\ContractService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function __construct(
        private AuditLogService $audit,
        private ContractService $contractService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', MaintenanceContract::class);

        $contracts = MaintenanceContract::with(['customer', 'generator', 'creator'])
            ->when($request->search, fn($q) =>
                $q->where('ref_number', 'like', "%{$request->search}%")
                  ->orWhereHas('customer', fn($cq) =>
                      $cq->where('company_name', 'like', "%{$request->search}%")
                  )
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $this->authorize('create', MaintenanceContract::class);
        $customers  = Customer::where('status', 'active')->orderBy('company_name')->get();
        $generators = Generator::with('customer')->where('status', 'active')->orderBy('serial_number')->get();
        return view('dashboard.contracts.create', compact('customers', 'generators'));
    }

    public function store(StoreContractRequest $request)
    {
        $this->authorize('create', MaintenanceContract::class);

        $contract = DB::transaction(function () use ($request) {
            $taxRate    = (float) ($request->tax_rate ?? 0);
            $subtotal   = (float) $request->subtotal;
            $calculated = $this->contractService->calculateValues($subtotal, $taxRate);

            $contract = MaintenanceContract::create([
                'customer_id'         => $request->customer_id,
                'generator_id'        => $request->generator_id,
                'created_by'          => Auth::id(),
                'ref_number'          => $this->contractService->generateRefNumber(),
                'to_name'             => $request->to_name,
                'project'             => $request->project,
                'status'              => ContractStatus::DRAFT,
                'contract_start_date' => $request->contract_start_date,
                'contract_end_date'   => $request->contract_end_date,
                'visit_count'         => $request->visit_count,
                'payment_method'      => $request->payment_method,
                'subtotal'            => $subtotal,
                'tax_rate'            => $taxRate,
                'tax_amount'          => $calculated['tax_amount'],
                'total_value'         => $calculated['total_value'],
                'currency'            => $request->currency ?? 'SAR',
                'terms'               => $request->terms,
                'notes'               => $request->notes,
            ]);

            $this->audit->log(
                action: 'contract_created',
                entityType: MaintenanceContract::class,
                entityId: $contract->id,
                newValues: ['ref_number' => $contract->ref_number, 'total_value' => $contract->total_value]
            );

            return $contract;
        });

        return redirect()->route('dashboard.contracts.show', $contract)
            ->with('success', 'ØªÙ… Ø¥Ù†Ø´Ø§Ø¡ Ø§Ù„Ø¹Ù‚Ø¯ ' . $contract->ref_number . ' Ø¨Ù†Ø¬Ø§Ø­');
    }

    public function show(MaintenanceContract $contract)
    {
        $this->authorize('view', $contract);
        $contract->load(['customer', 'generator', 'creator', 'visits.technician']);
        return view('dashboard.contracts.show', compact('contract'));
    }

    public function edit(MaintenanceContract $contract)
    {
        $this->authorize('update', $contract);
        $customers  = Customer::where('status', 'active')->orderBy('company_name')->get();
        $generators = Generator::with('customer')->where('status', 'active')->orderBy('serial_number')->get();
        return view('dashboard.contracts.edit', compact('contract', 'customers', 'generators'));
    }

    public function update(UpdateContractRequest $request, MaintenanceContract $contract)
    {
        $this->authorize('update', $contract);

        DB::transaction(function () use ($request, $contract) {
            $taxRate    = (float) ($request->tax_rate ?? 0);
            $subtotal   = (float) $request->subtotal;
            $calculated = $this->contractService->calculateValues($subtotal, $taxRate);

            $contract->update([
                'customer_id'         => $request->customer_id,
                'generator_id'        => $request->generator_id,
                'to_name'             => $request->to_name,
                'project'             => $request->project,
                'contract_start_date' => $request->contract_start_date,
                'contract_end_date'   => $request->contract_end_date,
                'visit_count'         => $request->visit_count,
                'payment_method'      => $request->payment_method,
                'subtotal'            => $subtotal,
                'tax_rate'            => $taxRate,
                'tax_amount'          => $calculated['tax_amount'],
                'total_value'         => $calculated['total_value'],
                'currency'            => $request->currency,
                'terms'               => $request->terms,
                'notes'               => $request->notes,
            ]);

            $this->audit->log(
                action: 'contract_updated',
                entityType: MaintenanceContract::class,
                entityId: $contract->id
            );
        });

        return redirect()->route('dashboard.contracts.show', $contract)
            ->with('success', 'ØªÙ… ØªØ­Ø¯ÙŠØ« Ø§Ù„Ø¹Ù‚Ø¯ Ø¨Ù†Ø¬Ø§Ø­');
    }

    public function destroy(MaintenanceContract $contract)
    {
        $this->authorize('delete', $contract);

        $this->audit->log(
            action: 'contract_deleted',
            entityType: MaintenanceContract::class,
            entityId: $contract->id,
            oldValues: ['ref_number' => $contract->ref_number]
        );

        $contract->delete();

        return redirect()->route('dashboard.contracts.index')
            ->with('success', 'ØªÙ… Ø­Ø°Ù Ø§Ù„Ø¹Ù‚Ø¯ Ø¨Ù†Ø¬Ø§Ø­');
    }

    public function activate(MaintenanceContract $contract)
    {
        $this->authorize('activate', $contract);

        DB::transaction(function () use ($contract) {
            $this->contractService->activate($contract);

            $this->audit->log(
                action: 'contract_activated',
                entityType: MaintenanceContract::class,
                entityId: $contract->id,
                newValues: ['status' => 'active', 'visits_scheduled' => $contract->visit_count]
            );
        });

        return redirect()->route('dashboard.contracts.show', $contract)
            ->with('success', 'ØªÙ… ØªÙØ¹ÙŠÙ„ Ø§Ù„Ø¹Ù‚Ø¯ ÙˆØ¬Ø¯ÙˆÙ„Ø© Ø§Ù„Ø²ÙŠØ§Ø±Ø§Øª Ø¨Ù†Ø¬Ø§Ø­');
    }

    public function terminate(MaintenanceContract $contract)
    {
        $this->authorize('terminate', $contract);

        DB::transaction(function () use ($contract) {
            $this->contractService->terminate($contract);

            $this->audit->log(
                action: 'contract_terminated',
                entityType: MaintenanceContract::class,
                entityId: $contract->id,
                newValues: ['status' => 'terminated']
            );
        });

        return redirect()->route('dashboard.contracts.show', $contract)
            ->with('success', 'ØªÙ… Ø¥Ù†Ù‡Ø§Ø¡ Ø§Ù„Ø¹Ù‚Ø¯ ÙˆØ¥Ù„ØºØ§Ø¡ Ø§Ù„Ø²ÙŠØ§Ø±Ø§Øª Ø§Ù„Ù…Ø¹Ù„Ù‚Ø©');
    }

    public function downloadPdf(MaintenanceContract $contract)
    {
        $this->authorize('downloadPdf', $contract);
        $contract->load(['customer', 'generator', 'creator']);

        $pdf = Pdf::loadView('pdf.contract', compact('contract'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('contract-' . $contract->ref_number . '.pdf');
    }
}

