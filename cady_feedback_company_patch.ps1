# CADY company feedback patch
# Run from PowerShell. It patches quotation creation, JOD currency, tax options,
# rental control page, pending customer activation, and admin dashboard links.

$project = "C:\Users\azmih\.gemini\antigravity\scratch\cady-est"
Set-Location $project
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)

function Write-NoBom($path, $content) {
    $full = Join-Path $project $path
    $dir = Split-Path $full
    if (!(Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
    [System.IO.File]::WriteAllText($full, $content, $utf8NoBom)
}

# 1) Quotation item logic: use quantity column, not qty
Write-NoBom "app\Services\QuotationService.php" @'
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
'@

# 2) Store quotation request: existing OR new customer, real quotation types, quantity, JOD/tax support
Write-NoBom "app\Http\Requests\StoreQuotationRequest.php" @'
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_mode' => ['required', Rule::in(['existing', 'new'])],
            'customer_id' => ['required_if:customer_mode,existing', 'nullable', 'exists:customers,id'],

            'new_customer.company_name' => ['required_if:customer_mode,new', 'nullable', 'string', 'max:255'],
            'new_customer.contact_person' => ['required_if:customer_mode,new', 'nullable', 'string', 'max:255'],
            'new_customer.phone' => ['required_if:customer_mode,new', 'nullable', 'string', 'max:30'],
            'new_customer.email' => ['nullable', 'email', 'max:255'],
            'new_customer.address' => ['nullable', 'string', 'max:500'],
            'new_customer.business_activity' => ['nullable', 'string', 'max:255'],

            'type' => ['required', Rule::in(['sale', 'rental', 'maintenance_contract', 'spare_parts', 'other'])],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:quotation_date'],
            'project' => ['nullable', 'string', 'max:255'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', Rule::in([0, 8, 16])],
            'currency' => ['required', Rule::in(['JOD', 'USD'])],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_mode.required' => 'اختر نوع العميل: عميل موجود أو عميل جديد.',
            'customer_id.required_if' => 'اختر العميل من النظام.',
            'new_customer.company_name.required_if' => 'اسم الشركة للعميل الجديد مطلوب.',
            'new_customer.contact_person.required_if' => 'اسم جهة الاتصال للعميل الجديد مطلوب.',
            'new_customer.phone.required_if' => 'رقم الهاتف للعميل الجديد مطلوب.',
            'type.required' => 'نوع عرض السعر مطلوب.',
            'type.in' => 'نوع عرض السعر غير صحيح.',
            'quotation_date.required' => 'تاريخ العرض مطلوب.',
            'valid_until.required' => 'تاريخ انتهاء الصلاحية مطلوب.',
            'valid_until.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ الإصدار.',
            'currency.required' => 'العملة مطلوبة.',
            'tax_rate.in' => 'نسبة الضريبة يجب أن تكون 0 أو 8 أو 16.',
            'items.required' => 'يجب إضافة بند واحد على الأقل.',
            'items.*.description.required' => 'وصف البند مطلوب.',
            'items.*.quantity.required' => 'الكمية مطلوبة.',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر.',
            'items.*.unit_price.required' => 'سعر الوحدة مطلوب.',
        ];
    }
}
'@

# 3) Update quotation request
Write-NoBom "app\Http\Requests\UpdateQuotationRequest.php" @'
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'type' => ['required', Rule::in(['sale', 'rental', 'maintenance_contract', 'spare_parts', 'other'])],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:quotation_date'],
            'project' => ['nullable', 'string', 'max:255'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', Rule::in([0, 8, 16])],
            'currency' => ['required', Rule::in(['JOD', 'USD'])],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
'@

# 4) Quotation controller: supports new customer while creating quotation, JOD default, admin/sales logic
Write-NoBom "app\Http\Controllers\Dashboard\QuotationController.php" @'
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
'@

# 5) Replace quotation create page with company-ready version
Write-NoBom "resources\views\dashboard\quotations\create.blade.php" @'
@extends('layouts.dashboard')

@section('title', 'إنشاء عرض سعر - CADY EST')
@section('page_title', 'إنشاء عرض سعر جديد')

@section('content')
<form action="{{ route('dashboard.quotations.store') }}" method="POST" x-data="quotationBuilder()" x-init="calcTotals()" class="space-y-6">
    @csrf

    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-4 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بيانات العميل</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <label class="flex items-center gap-2 rounded-xl border border-gray-200 p-3 cursor-pointer">
                        <input type="radio" name="customer_mode" value="existing" x-model="customerMode" class="text-[#00d26a]" checked>
                        <span class="font-semibold text-sm">عميل موجود في النظام</span>
                    </label>
                    <label class="flex items-center gap-2 rounded-xl border border-gray-200 p-3 cursor-pointer">
                        <input type="radio" name="customer_mode" value="new" x-model="customerMode" class="text-[#00d26a]">
                        <span class="font-semibold text-sm">عميل جديد</span>
                    </label>
                </div>

                <div x-show="customerMode === 'existing'">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">اختر العميل</label>
                    <select name="customer_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                        <option value="">اختر العميل...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->company_name }} - {{ $customer->contact_person }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="customerMode === 'new'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">اسم الشركة</label>
                        <input type="text" name="new_customer[company_name]" value="{{ old('new_customer.company_name') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">اسم الشخص المسؤول</label>
                        <input type="text" name="new_customer[contact_person]" value="{{ old('new_customer.contact_person') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الهاتف</label>
                        <input type="text" name="new_customer[phone]" value="{{ old('new_customer.phone') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">البريد الإلكتروني</label>
                        <input type="email" name="new_customer[email]" value="{{ old('new_customer.email') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">النشاط</label>
                        <input type="text" name="new_customer[business_activity]" value="{{ old('new_customer.business_activity') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm" placeholder="صناعات / طبي / تجاري">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العنوان</label>
                        <input type="text" name="new_customer[address]" value="{{ old('new_customer.address') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بيانات العرض</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">نوع العرض</label>
                        <select name="type" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            <option value="sale" {{ old('type') == 'sale' ? 'selected' : '' }}>بيع مولد</option>
                            <option value="rental" {{ old('type') == 'rental' ? 'selected' : '' }}>تأجير مولد</option>
                            <option value="maintenance_contract" {{ old('type') == 'maintenance_contract' ? 'selected' : '' }}>عقد صيانة</option>
                            <option value="spare_parts" {{ old('type') == 'spare_parts' ? 'selected' : '' }}>قطع غيار</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">المشروع / الوصف</label>
                        <input type="text" name="project" value="{{ old('project') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ العرض</label>
                        <input type="date" name="quotation_date" value="{{ old('quotation_date', now()->toDateString()) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">صالح حتى</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', now()->addDays(30)->toDateString()) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-[#0b192c]">بنود عرض السعر</h3>
                    <button type="button" @click="addItem()" class="bg-[#0b192c] text-white rounded-lg px-3 py-2 text-xs font-bold">+ إضافة بند</button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 items-end bg-gray-50 rounded-xl p-3 mb-3">
                        <div class="col-span-5">
                            <label class="text-xs text-gray-500 mb-1 block">البند / الوصف</label>
                            <input type="text" :name="`items[${index}][description]`" x-model="item.description" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="مثال: مولد بيركنز 50 KVA">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">الكمية</label>
                            <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="calcItem(index)" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">سعر الوحدة</label>
                            <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="calcItem(index)" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">الإجمالي</label>
                            <input type="text" :value="item.total.toFixed(2)" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-semibold">
                        </div>
                        <div class="col-span-1">
                            <button type="button" @click="removeItem(index)" class="w-full text-red-500 py-2">✕</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملاحظات</h3>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملخص العرض</h3>
                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between"><span>قبل الخصم</span><span x-text="money(subtotal)"></span></div>
                    <div class="flex justify-between items-center">
                        <label>الخصم</label>
                        <input type="number" name="discount" x-model.number="discount" @input="calcTotals()" min="0" step="0.01" class="w-28 px-2 py-1 border border-gray-200 rounded-lg text-xs text-left">
                    </div>
                    <div class="flex justify-between items-center">
                        <label>نسبة الضريبة</label>
                        <select name="tax_rate" x-model.number="taxRate" @change="calcTotals()" class="w-28 px-2 py-1 border border-gray-200 rounded-lg text-xs">
                            <option value="16">16%</option>
                            <option value="8">8%</option>
                            <option value="0">0%</option>
                        </select>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500"><span>مبلغ الضريبة</span><span x-text="money(taxAmount)"></span></div>
                    <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-3"><span>الإجمالي النهائي</span><span class="text-[#00d26a]" x-text="money(total)"></span></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">العملة</label>
                    <select name="currency" x-model="currency" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="JOD">JOD - دينار أردني</option>
                        <option value="USD">USD - دولار أمريكي</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm">حفظ عرض السعر</button>
                <a href="{{ route('dashboard.quotations.index') }}" class="block text-center w-full mt-3 text-gray-500 text-sm">إلغاء</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function quotationBuilder() {
    return {
        customerMode: '{{ old('customer_mode', 'existing') }}',
        currency: '{{ old('currency', 'JOD') }}',
        items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
        discount: Number('{{ old('discount', 0) }}'),
        taxRate: Number('{{ old('tax_rate', 16) }}'),
        subtotal: 0,
        taxAmount: 0,
        total: 0,
        addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 }); },
        removeItem(index) { if (this.items.length > 1) { this.items.splice(index, 1); this.calcTotals(); } },
        calcItem(index) {
            const item = this.items[index];
            item.total = Number(item.quantity || 0) * Number(item.unit_price || 0);
            this.calcTotals();
        },
        calcTotals() {
            this.subtotal = this.items.reduce((sum, i) => sum + Number(i.total || 0), 0);
            const taxable = Math.max(0, this.subtotal - Number(this.discount || 0));
            this.taxAmount = taxable * (Number(this.taxRate || 0) / 100);
            this.total = taxable + this.taxAmount;
        },
        money(value) { return Number(value || 0).toFixed(2) + ' ' + this.currency; }
    }
}
</script>
@endpush
@endsection
'@

# 6) Fix existing quotation views/PDF for quantity/JOD/type values
$filesToPatch = @(
    "resources\views\dashboard\quotations\edit.blade.php",
    "resources\views\dashboard\quotations\show.blade.php",
    "resources\views\dashboard\quotations\index.blade.php",
    "resources\views\pdf\quotation.blade.php"
)
foreach ($file in $filesToPatch) {
    $path = Join-Path $project $file
    if (Test-Path $path) {
        $c = Get-Content $path -Raw
        $c = $c -replace '\bqty\b', 'quantity'
        $c = $c -replace 'SAR', 'JOD'
        $c = $c -replace 'ريال سعودي', 'دينار أردني'
        $c = $c -replace 'value="parts"', 'value="spare_parts"'
        $c = $c -replace "== 'parts'", "== 'spare_parts'"
        $c = $c -replace 'value="maintenance"', 'value="maintenance_contract"'
        $c = $c -replace "== 'maintenance'", "== 'maintenance_contract'"
        $c = $c -replace 'value="installation"', 'value="sale"'
        $c = $c -replace "== 'installation'", "== 'sale'"
        [System.IO.File]::WriteAllText($path, $c, $utf8NoBom)
    }
}

# 7) Rental control page using existing rental quotations + rented generators
Write-NoBom "app\Http\Controllers\Dashboard\RentalController.php" @'
<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\GeneratorStatus;
use App\Http\Controllers\Controller;
use App\Models\Generator;
use App\Models\Quotation;

class RentalController extends Controller
{
    public function index()
    {
        $rentalQuotations = Quotation::with('customer')
            ->where('type', 'rental')
            ->latest()
            ->paginate(10);

        $rentedGenerators = Generator::with('customer')
            ->where('status', GeneratorStatus::RENTED)
            ->orderBy('serial_number')
            ->get();

        return view('dashboard.rentals.index', compact('rentalQuotations', 'rentedGenerators'));
    }
}
'@

Write-NoBom "resources\views\dashboard\rentals\index.blade.php" @'
@extends('layouts.dashboard')

@section('title', 'Rental Control - CADY EST')
@section('page_title', 'Rental Control / إدارة التأجير')

@section('content')
<div class="space-y-6" dir="rtl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#0b192c]">إدارة التأجير</h2>
            <p class="text-sm text-gray-500 mt-1">متابعة عروض التأجير والمولدات المؤجرة.</p>
        </div>
        <a href="{{ route('dashboard.quotations.create') }}" class="bg-[#00d26a] text-white px-4 py-2 rounded-xl text-sm font-bold">+ إنشاء عرض تأجير</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-sm text-gray-500">عروض التأجير</p>
            <p class="text-3xl font-bold text-[#0b192c] mt-2">{{ $rentalQuotations->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-sm text-gray-500">المولدات المؤجرة حالياً</p>
            <p class="text-3xl font-bold text-[#0b192c] mt-2">{{ $rentedGenerators->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 font-bold text-[#0b192c]">عروض التأجير</div>
        <table class="w-full text-sm">
            <thead class="bg-[#0b192c] text-white">
                <tr>
                    <th class="px-5 py-3 text-right">رقم العرض</th>
                    <th class="px-5 py-3 text-right">العميل</th>
                    <th class="px-5 py-3 text-right">التاريخ</th>
                    <th class="px-5 py-3 text-right">الإجمالي</th>
                    <th class="px-5 py-3 text-right">الحالة</th>
                    <th class="px-5 py-3 text-right">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rentalQuotations as $quotation)
                    <tr>
                        <td class="px-5 py-3 font-bold">{{ $quotation->ref_number }}</td>
                        <td class="px-5 py-3">{{ $quotation->customer?->company_name }}</td>
                        <td class="px-5 py-3">{{ $quotation->quotation_date?->format('Y-m-d') }}</td>
                        <td class="px-5 py-3">{{ $quotation->currency }} {{ number_format($quotation->total, 2) }}</td>
                        <td class="px-5 py-3"><x-badge :status="$quotation->status" /></td>
                        <td class="px-5 py-3"><a class="text-blue-600 font-bold" href="{{ route('dashboard.quotations.show', $quotation) }}">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">لا توجد عروض تأجير بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $rentalQuotations->links() }}</div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 font-bold text-[#0b192c]">المولدات المؤجرة</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-right">الرقم التسلسلي</th>
                    <th class="px-5 py-3 text-right">الماركة / الموديل</th>
                    <th class="px-5 py-3 text-right">القدرة</th>
                    <th class="px-5 py-3 text-right">العميل</th>
                    <th class="px-5 py-3 text-right">الموقع</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rentedGenerators as $generator)
                    <tr>
                        <td class="px-5 py-3 font-bold">{{ $generator->serial_number }}</td>
                        <td class="px-5 py-3">{{ $generator->brand }} / {{ $generator->model }}</td>
                        <td class="px-5 py-3">{{ $generator->capacity_kva }} KVA</td>
                        <td class="px-5 py-3">{{ $generator->customer?->company_name ?? 'غير مرتبط' }}</td>
                        <td class="px-5 py-3">{{ $generator->location }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">لا توجد مولدات مؤجرة حالياً.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
'@

# 8) Add rental route and import if missing
$routes = Join-Path $project "routes\web.php"
$c = Get-Content $routes -Raw
if ($c -notmatch 'RentalController') {
    $c = $c -replace 'use App\\Http\\Controllers\\Dashboard\\VisitController;', "use App\Http\Controllers\Dashboard\VisitController;`r`nuse App\Http\Controllers\Dashboard\RentalController;"
}
if ($c -notmatch "rentals.index") {
    $insert = @'

            // Rental Control (Admin/Sales view and manage rental offers)
            Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index')->middleware('role:admin,sales');
'@
    $c = $c -replace "\s+// Quotations CRUD", "$insert`r`n            // Quotations CRUD"
}
[System.IO.File]::WriteAllText($routes, $c, $utf8NoBom)

# 9) Add rental link to sidebar if missing
$layout = Join-Path $project "resources\views\layouts\dashboard.blade.php"
$c = Get-Content $layout -Raw
if ($c -notmatch 'dashboard.rentals.index') {
    $rentalLink = @'

            @if(Auth::user()->isAdmin() || Auth::user()->isSales())
                <a href="{{ route('dashboard.rentals.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/rentals') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">📅</span> Rental Control / التأجير
                </a>
            @endif
'@
    $c = $c -replace "\s+<!-- Quotations \(Admin/Sales only\) -->", "$rentalLink`r`n            <!-- Quotations (Admin/Sales only) -->"
}
[System.IO.File]::WriteAllText($layout, $c, $utf8NoBom)

# 10) Make approval activate/deactivate linked portal users and fix search for SQLite/Postgres compatibility
$customerController = Join-Path $project "app\Http\Controllers\Dashboard\CustomerController.php"
$c = Get-Content $customerController -Raw
$c = $c -replace "'ilike'", "'like'"
$c = $c -replace "\$customer->update\(\['status' => CustomerStatus::ACTIVE\]\);", "`$customer->update(['status' => CustomerStatus::ACTIVE]);`r`n            `$customer->users()->update(['is_active' => true]);"
$c = $c -replace "\$customer->update\(\['status' => CustomerStatus::INACTIVE\]\);", "`$customer->update(['status' => CustomerStatus::INACTIVE]);`r`n            `$customer->users()->update(['is_active' => false]);"
$c = $c -replace "\$customer->update\(\['status' => CustomerStatus::SUSPENDED\]\);", "`$customer->update(['status' => CustomerStatus::SUSPENDED]);`r`n            `$customer->users()->update(['is_active' => false]);"
[System.IO.File]::WriteAllText($customerController, $c, $utf8NoBom)

# 11) Clear and build
php artisan optimize:clear
npm run build
php artisan route:list | Out-Host

Write-Host "Patch finished. Test /dashboard/quotations/create and /dashboard/rentals, then commit and push." -ForegroundColor Green
