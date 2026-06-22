<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\CustomerStatus;
use App\Enums\QuotationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Customer;
use App\Models\Quotation;
use App\Services\AuditLogService;
use App\Services\QuotationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function __construct(
        private AuditLogService $audit,
        private QuotationService $quotationService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Quotation::class);

        $quotations = Quotation::with(['customer', 'creator'])
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($qq) use ($search) {
                    $qq->whereHas('customer', fn($cq) => $cq->where('company_name', 'like', "%{$search}%"))
                       ->orWhere('ref_number', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $this->authorize('create', Quotation::class);
        $customers = Customer::where('status', CustomerStatus::ACTIVE)->orderBy('company_name')->get();
        return view('dashboard.quotations.create', compact('customers'));
    }

    public function store(StoreQuotationRequest $request)
    {
        $this->authorize('create', Quotation::class);

        $quotation = DB::transaction(function () use ($request) {
            if ($request->customer_mode === 'new') {
                $new = $request->input('new_customer', []);

                $customer = Customer::create([
                    'company_name' => $new['company_name'],
                    'contact_person' => $new['contact_person'],
                    'phone' => $new['phone'],
                    'email' => $new['email'] ?? null,
                    'address' => $new['address'] ?? null,
                    'business_activity' => $new['business_activity'] ?? 'غير محدد',
                    'status' => CustomerStatus::ACTIVE,
                ]);

                $customerId = $customer->id;
            } else {
                $customerId = $request->customer_id;
            }

            $quotation = Quotation::create([
                'customer_id' => $customerId,
                'created_by' => Auth::id(),
                'ref_number' => $this->quotationService->generateRefNumber(),
                'type' => $request->type,
                'status' => QuotationStatus::DRAFT,
                'quotation_date' => $request->quotation_date,
                'valid_until' => $request->valid_until,
                'project' => $request->project,
                'discount' => $request->discount ?? 0,
                'tax_rate' => $request->tax_rate ?? 16,
                'currency' => $request->currency ?? 'JOD',
                'notes' => $request->notes,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total' => 0,
            ]);

            $this->quotationService->syncItems($quotation, $request->items);

            return $quotation;
        });

        $this->audit->log(
            action: 'quotation_created',
            entityType: Quotation::class,
            entityId: $quotation->id,
            newValues: ['ref_number' => $quotation->ref_number, 'total' => $quotation->total]
        );

        return redirect()->route('dashboard.quotations.show', $quotation)
            ->with('success', 'تم إنشاء عرض السعر ' . $quotation->ref_number . ' بنجاح');
    }

    public function show(Quotation $quotation)
    {
        $this->authorize('view', $quotation);
        $quotation->load(['customer', 'creator', 'items']);
        return view('dashboard.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $this->authorize('update', $quotation);
        $customers = Customer::where('status', CustomerStatus::ACTIVE)->orderBy('company_name')->get();
        $quotation->load('items');
        return view('dashboard.quotations.edit', compact('quotation', 'customers'));
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $this->authorize('update', $quotation);

        DB::transaction(function () use ($request, $quotation) {
            $quotation->update([
                'customer_id' => $request->customer_id,
                'type' => $request->type,
                'quotation_date' => $request->quotation_date,
                'valid_until' => $request->valid_until,
                'project' => $request->project,
                'discount' => $request->discount ?? 0,
                'tax_rate' => $request->tax_rate ?? 16,
                'currency' => $request->currency ?? 'JOD',
                'notes' => $request->notes,
            ]);

            $this->quotationService->syncItems($quotation, $request->items);
        });

        $this->audit->log(
            action: 'quotation_updated',
            entityType: Quotation::class,
            entityId: $quotation->id
        );

        return redirect()->route('dashboard.quotations.show', $quotation)
            ->with('success', 'تم تحديث عرض السعر بنجاح');
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorize('delete', $quotation);

        $this->audit->log(
            action: 'quotation_deleted',
            entityType: Quotation::class,
            entityId: $quotation->id,
            oldValues: ['ref_number' => $quotation->ref_number]
        );

        $quotation->delete();

        return redirect()->route('dashboard.quotations.index')
            ->with('success', 'تم حذف عرض السعر بنجاح');
    }

    public function markSent(Quotation $quotation)
    {
        $this->authorize('update', $quotation);

        if ($quotation->status !== QuotationStatus::DRAFT) {
            return back()->with('error', 'لا يمكن إرسال هذا العرض في وضعه الحالي');
        }

        $quotation->update(['status' => QuotationStatus::SENT]);
        $this->audit->log('quotation_sent', entityType: Quotation::class, entityId: $quotation->id);

        return back()->with('success', 'تم تحديث حالة العرض إلى مرسل');
    }

    public function accept(Quotation $quotation)
    {
        $this->authorize('update', $quotation);

        if ($quotation->status !== QuotationStatus::SENT) {
            return back()->with('error', 'يمكن قبول العروض المرسلة فقط');
        }

        $quotation->update(['status' => QuotationStatus::ACCEPTED]);
        $this->audit->log('quotation_accepted', entityType: Quotation::class, entityId: $quotation->id);

        return back()->with('success', 'تم تحديث حالة العرض إلى مقبول');
    }

    public function reject(Quotation $quotation)
    {
        $this->authorize('update', $quotation);

        if ($quotation->status !== QuotationStatus::SENT) {
            return back()->with('error', 'يمكن رفض العروض المرسلة فقط');
        }

        $quotation->update(['status' => QuotationStatus::REJECTED]);
        $this->audit->log('quotation_rejected', entityType: Quotation::class, entityId: $quotation->id);

        return back()->with('success', 'تم تحديث حالة العرض إلى مرفوض');
    }

    public function downloadPdf(Quotation $quotation)
    {
        $this->authorize('view', $quotation);
        $quotation->load(['customer', 'creator', 'items']);

        $pdf = Pdf::loadView('pdf.quotation', compact('quotation'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('quotation-' . $quotation->ref_number . '.pdf');
    }
}