<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\MaintenanceContract;
use App\Models\Quotation;
use App\Models\Rental;
use App\Models\WhatsAppMessage;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WhatsAppController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request)
    {
        $messages = WhatsAppMessage::with(['customer', 'quotation', 'rental', 'maintenanceContract', 'creator'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('message_type'), fn ($q) => $q->where('message_type', $request->message_type))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->search);

                $q->where(function ($query) use ($search) {
                    $query->where('phone', 'like', "%{$search}%")
                        ->orWhere('message_body', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('company_name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.whatsapp.index', compact('messages'));
    }

    public function create()
    {
        return view('dashboard.whatsapp.create', [
            'customers' => Customer::orderBy('company_name')->get(),
            'quotations' => Quotation::with('customer')->latest()->limit(100)->get(),
            'rentals' => Rental::with('customer')->latest()->limit(100)->get(),
            'contracts' => MaintenanceContract::with('customer')->latest()->limit(100)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'uuid', Rule::exists('customers', 'id')],
            'quotation_id' => ['nullable', 'uuid', Rule::exists('quotations', 'id')],
            'rental_id' => ['nullable', 'uuid', Rule::exists('rentals', 'id')],
            'maintenance_contract_id' => ['nullable', 'uuid', Rule::exists('maintenance_contracts', 'id')],
            'phone' => ['required', 'string', 'max:30'],
            'message_type' => ['required', 'string', Rule::in(['general', 'quotation', 'rental', 'maintenance', 'payment', 'support'])],
            'message_body' => ['required', 'string', 'max:5000'],
        ]);

        $phone = $this->normalizePhone($data['phone']);
        $whatsappUrl = 'https://wa.me/' . $phone . '?text=' . rawurlencode($data['message_body']);

        $message = WhatsAppMessage::create([
            'customer_id' => $data['customer_id'] ?? null,
            'quotation_id' => $data['quotation_id'] ?? null,
            'rental_id' => $data['rental_id'] ?? null,
            'maintenance_contract_id' => $data['maintenance_contract_id'] ?? null,
            'phone' => $phone,
            'message_type' => $data['message_type'],
            'status' => 'draft',
            'message_body' => $data['message_body'],
            'whatsapp_url' => $whatsappUrl,
            'created_by' => $request->user()?->id,
        ]);

        $this->log($request, 'whatsapp.created', $message, [], $message->toArray());

        return redirect()
            ->route('dashboard.whatsapp.show', $message)
            ->with('success', 'WhatsApp message record created successfully.');
    }

    public function show(WhatsAppMessage $whatsapp)
    {
        $whatsapp->load(['customer', 'quotation', 'rental', 'maintenanceContract', 'creator']);

        return view('dashboard.whatsapp.show', [
            'message' => $whatsapp,
        ]);
    }

    public function open(Request $request, WhatsAppMessage $whatsapp)
    {
        $oldValues = $whatsapp->toArray();

        $whatsapp->update([
            'status' => 'opened',
            'opened_at' => now(),
        ]);

        $this->log($request, 'whatsapp.opened', $whatsapp, $oldValues, $whatsapp->fresh()->toArray());

        return redirect()->away($whatsapp->whatsapp_url);
    }

    public function markSent(Request $request, WhatsAppMessage $whatsapp)
    {
        $oldValues = $whatsapp->toArray();

        $whatsapp->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->log($request, 'whatsapp.sent', $whatsapp, $oldValues, $whatsapp->fresh()->toArray());

        return back()->with('success', 'WhatsApp message marked as sent.');
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '00')) {
            return substr($phone, 2);
        }

        if (str_starts_with($phone, '0')) {
            return '962' . substr($phone, 1);
        }

        return $phone;
    }

    private function log(Request $request, string $action, WhatsAppMessage $message, array $oldValues, array $newValues): void
    {
        try {
            $this->auditLogService->log(
                action: $action,
                entityType: WhatsAppMessage::class,
                entityId: $message->id,
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