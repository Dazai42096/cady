<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeneratorRequest;
use App\Http\Requests\UpdateGeneratorRequest;
use App\Models\Customer;
use App\Models\Generator;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GeneratorController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLog
    ) {}

    /**
     * Display a paginated list of generators with search & filter support.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Generator::class);

        $query = Generator::with('customer')->latest();

        // Search by serial number, brand, or model
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter by customer
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $generators = $query->paginate(15)->withQueryString();

        // Pass customers for the filter dropdown
        $customers = Customer::orderBy('company_name')->get(['id', 'company_name']);

        return view('dashboard.generators.index', compact('generators', 'customers'));
    }

    /**
     * Show the form for creating a new generator.
     */
    public function create(): View
    {
        $this->authorize('create', Generator::class);

        $customers = Customer::orderBy('company_name')->get(['id', 'company_name']);

        return view('dashboard.generators.create', compact('customers'));
    }

    /**
     * Store a newly created generator in the database.
     */
    public function store(StoreGeneratorRequest $request): RedirectResponse
    {
        $this->authorize('create', Generator::class);

        $generator = DB::transaction(function () use ($request) {
            $generator = Generator::create($request->validated());

            $this->auditLog->log(
                action: 'generator.create',
                entityType: Generator::class,
                entityId: $generator->id,
                newValues: $generator->toArray(),
                metadata: ['serial_number' => $generator->serial_number]
            );

            return $generator;
        });

        return redirect()
            ->route('dashboard.generators.show', $generator)
            ->with('success', "ØªÙ… Ø¥Ø¶Ø§ÙØ© Ø§Ù„Ù…ÙˆÙ„Ø¯ Â«{$generator->brand} {$generator->model}Â» Ø¨Ù†Ø¬Ø§Ø­.");
    }

    /**
     * Display the specified generator with related data.
     */
    public function show(Generator $generator): View
    {
        $this->authorize('view', $generator);

        $generator->load(['customer', 'maintenanceContracts.customer']);

        return view('dashboard.generators.show', compact('generator'));
    }

    /**
     * Show the form for editing the specified generator.
     */
    public function edit(Generator $generator): View
    {
        $this->authorize('update', $generator);

        $customers = Customer::orderBy('company_name')->get(['id', 'company_name']);

        return view('dashboard.generators.edit', compact('generator', 'customers'));
    }

    /**
     * Update the specified generator in the database.
     */
    public function update(UpdateGeneratorRequest $request, Generator $generator): RedirectResponse
    {
        $this->authorize('update', $generator);

        DB::transaction(function () use ($request, $generator) {
            $oldValues = $generator->toArray();

            $generator->update($request->validated());

            $this->auditLog->log(
                action: 'generator.update',
                entityType: Generator::class,
                entityId: $generator->id,
                oldValues: $oldValues,
                newValues: $generator->fresh()->toArray(),
                metadata: ['serial_number' => $generator->serial_number]
            );
        });

        return redirect()
            ->route('dashboard.generators.show', $generator)
            ->with('success', "ØªÙ… ØªØ­Ø¯ÙŠØ« Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ù…ÙˆÙ„Ø¯ Â«{$generator->brand} {$generator->model}Â» Ø¨Ù†Ø¬Ø§Ø­.");
    }

    /**
     * Soft-delete the specified generator.
     */
    public function destroy(Generator $generator): RedirectResponse
    {
        $this->authorize('delete', $generator);

        DB::transaction(function () use ($generator) {
            $this->auditLog->log(
                action: 'generator.delete',
                entityType: Generator::class,
                entityId: $generator->id,
                oldValues: $generator->toArray(),
                metadata: ['serial_number' => $generator->serial_number]
            );

            $generator->delete();
        });

        return redirect()
            ->route('dashboard.generators.index')
            ->with('success', "ØªÙ… Ø­Ø°Ù Ø§Ù„Ù…ÙˆÙ„Ø¯ Â«{$generator->brand} {$generator->model}Â» Ø¨Ù†Ø¬Ø§Ø­.");
    }
}

