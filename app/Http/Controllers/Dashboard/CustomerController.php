<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\CustomerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    /**
     * Display a paginated listing of customers with search.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query()->with('generators');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('dashboard.customers.index', compact('customers', 'search'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        $this->authorize('create', Customer::class);

        return view('dashboard.customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);

        $customer = DB::transaction(function () use ($request) {
            $customer = Customer::create([
                ...$request->validated(),
                'status' => CustomerStatus::PENDING_ADMIN_LINK,
            ]);

            $this->auditLogService->log(
                action: 'customer_created',
                entityType: Customer::class,
                entityId: $customer->id,
                newValues: $customer->toArray()
            );

            return $customer;
        });

        return redirect()
            ->route('dashboard.customers.show', $customer)
            ->with('success', 'Ã˜ÂªÃ™â€¦ Ã˜Â¥Ã™â€ Ã˜Â´Ã˜Â§Ã˜Â¡ Ã˜Â§Ã™â€žÃ˜Â¹Ã™â€¦Ã™Å Ã™â€ž Ã˜Â¨Ã™â€ Ã˜Â¬Ã˜Â§Ã˜Â­ Ã™Ë†Ã™â€¡Ã™Ë† Ã™ÂÃ™Å  Ã˜Â§Ã™â€ Ã˜ÂªÃ˜Â¸Ã˜Â§Ã˜Â± Ã™â€¦Ã™Ë†Ã˜Â§Ã™ÂÃ™â€šÃ˜Â© Ã˜Â§Ã™â€žÃ˜Â¥Ã˜Â¯Ã˜Â§Ã˜Â±Ã˜Â©.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        $customer->load([
            'generators',
            'quotations',
            'maintenanceContracts',
        ]);

        $generatorsCount  = $customer->generators->count();
        $quotationsCount  = $customer->quotations->count();
        $contractsCount   = $customer->maintenanceContracts->count();

        return view('dashboard.customers.show', compact(
            'customer',
            'generatorsCount',
            'quotationsCount',
            'contractsCount'
        ));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        return view('dashboard.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);

        DB::transaction(function () use ($request, $customer) {
            $oldValues = $customer->toArray();

            $customer->update($request->validated());

            $this->auditLogService->log(
                action: 'customer_updated',
                entityType: Customer::class,
                entityId: $customer->id,
                oldValues: $oldValues,
                newValues: $customer->fresh()->toArray()
            );
        });

        return redirect()
            ->route('dashboard.customers.show', $customer)
            ->with('success', 'Ã˜ÂªÃ™â€¦ Ã˜ÂªÃ˜Â­Ã˜Â¯Ã™Å Ã˜Â« Ã˜Â¨Ã™Å Ã˜Â§Ã™â€ Ã˜Â§Ã˜Âª Ã˜Â§Ã™â€žÃ˜Â¹Ã™â€¦Ã™Å Ã™â€ž Ã˜Â¨Ã™â€ Ã˜Â¬Ã˜Â§Ã˜Â­.');
    }

    /**
     * Soft-delete the specified customer.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        DB::transaction(function () use ($customer) {
            $this->auditLogService->log(
                action: 'customer_deleted',
                entityType: Customer::class,
                entityId: $customer->id,
                oldValues: $customer->toArray()
            );

            $customer->delete();
        });

        return redirect()
            ->route('dashboard.customers.index')
            ->with('success', 'Ã˜ÂªÃ™â€¦ Ã˜Â­Ã˜Â°Ã™Â Ã˜Â§Ã™â€žÃ˜Â¹Ã™â€¦Ã™Å Ã™â€ž Ã˜Â¨Ã™â€ Ã˜Â¬Ã˜Â§Ã˜Â­.');
    }

    /**
     * Display all pending customers (admin only).
     */
    public function pending(Request $request): View
    {
        $this->authorize('approve', Customer::class);

        $customers = Customer::where('status', CustomerStatus::PENDING_ADMIN_LINK)
            ->latest()
            ->paginate(15);

        return view('dashboard.customers.pending', compact('customers'));
    }

    /**
     * Approve a pending customer (set to ACTIVE).
     */
    public function approve(Customer $customer): RedirectResponse
    {
        $this->authorize('approve', $customer);

        DB::transaction(function () use ($customer) {
            $oldStatus = $customer->status;
            $customer->update(['status' => CustomerStatus::ACTIVE]);
            $customer->users()->update(['is_active' => true]);

            $this->auditLogService->log(
                action: 'customer_approved',
                entityType: Customer::class,
                entityId: $customer->id,
                oldValues: ['status' => $oldStatus?->value],
                newValues: ['status' => CustomerStatus::ACTIVE->value]
            );
        });

        return redirect()
            ->route('dashboard.customers.pending')
            ->with('success', 'Ã˜ÂªÃ™â€¦ Ã˜ÂªÃ™ÂÃ˜Â¹Ã™Å Ã™â€ž Ã˜Â§Ã™â€žÃ˜Â¹Ã™â€¦Ã™Å Ã™â€ž Ã˜Â¨Ã™â€ Ã˜Â¬Ã˜Â§Ã˜Â­.');
    }

    /**
     * Reject a pending customer (set to INACTIVE).
     */
    public function reject(Customer $customer): RedirectResponse
    {
        $this->authorize('approve', $customer);

        DB::transaction(function () use ($customer) {
            $oldStatus = $customer->status;
            $customer->update(['status' => CustomerStatus::INACTIVE]);
            $customer->users()->update(['is_active' => false]);

            $this->auditLogService->log(
                action: 'customer_rejected',
                entityType: Customer::class,
                entityId: $customer->id,
                oldValues: ['status' => $oldStatus?->value],
                newValues: ['status' => CustomerStatus::INACTIVE->value]
            );
        });

        return redirect()
            ->route('dashboard.customers.pending')
            ->with('success', 'Ã˜ÂªÃ™â€¦ Ã˜Â±Ã™ÂÃ˜Â¶ Ã˜Â·Ã™â€žÃ˜Â¨ Ã˜Â§Ã™â€žÃ˜Â¹Ã™â€¦Ã™Å Ã™â€ž.');
    }

    /**
     * Suspend an active customer.
     */
    public function suspend(Customer $customer): RedirectResponse
    {
        $this->authorize('approve', $customer);

        DB::transaction(function () use ($customer) {
            $oldStatus = $customer->status;
            $customer->update(['status' => CustomerStatus::SUSPENDED]);
            $customer->users()->update(['is_active' => false]);

            $this->auditLogService->log(
                action: 'customer_suspended',
                entityType: Customer::class,
                entityId: $customer->id,
                oldValues: ['status' => $oldStatus?->value],
                newValues: ['status' => CustomerStatus::SUSPENDED->value]
            );
        });

        return redirect()
            ->route('dashboard.customers.show', $customer)
            ->with('success', 'Ã˜ÂªÃ™â€¦ Ã˜Â¥Ã™Å Ã™â€šÃ˜Â§Ã™Â Ã˜Â§Ã™â€žÃ˜Â¹Ã™â€¦Ã™Å Ã™â€ž Ã™â€¦Ã˜Â¤Ã™â€šÃ˜ÂªÃ˜Â§Ã™â€¹.');
    }
}

