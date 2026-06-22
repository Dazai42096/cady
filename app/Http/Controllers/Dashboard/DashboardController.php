<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Generator;
use App\Models\Quotation;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceVisit;
use App\Models\AuditLog;
use App\Models\QuoteRequest;
use App\Enums\CustomerStatus;
use App\Enums\GeneratorStatus;
use App\Enums\QuotationStatus;
use App\Enums\ContractStatus;
use App\Enums\VisitStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the main admin dashboard.
     */
    public function index()
    {
        $stats = [
            'active_customers' => Customer::where('status', CustomerStatus::ACTIVE)->count(),
            'pending_customers' => Customer::where('status', CustomerStatus::PENDING_ADMIN_LINK)->count(),
            'total_generators' => Generator::count(),
            'available_generators' => Generator::where('status', GeneratorStatus::AVAILABLE)->count(),
            'total_quotations' => Quotation::count(),
            'sent_quotations' => Quotation::where('status', QuotationStatus::SENT)->count(),
            'active_contracts' => MaintenanceContract::where('status', ContractStatus::ACTIVE)->count(),
            'total_contract_value' => MaintenanceContract::where('status', ContractStatus::ACTIVE)->sum('total_value'),
            'upcoming_visits' => MaintenanceVisit::whereIn('status', [VisitStatus::SCHEDULED, VisitStatus::CONFIRMED])
                ->where('planned_date', '>=', now()->toDateString())
                ->count(),
        ];

        // Fetch latest audit logs
        $latestLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.index', compact('stats', 'latestLogs'));
    }

    /**
     * View all quote requests.
     */
    public function quoteRequests()
    {
        $requests = QuoteRequest::orderBy('created_at', 'desc')->paginate(10);
        return view('dashboard.quote-requests', compact('requests'));
    }

    /**
     * Process / archive a quote request.
     */
    public function processQuoteRequest(QuoteRequest $quoteRequest)
    {
        $oldStatus = $quoteRequest->status;
        $quoteRequest->update(['status' => 'processed']);

        // Log audit log
        app(\App\Services\AuditLogService::class)->log(
            action: 'quote_request.process',
            entityType: QuoteRequest::class,
            entityId: $quoteRequest->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => 'processed']
        );

        return back()->with('success', 'تم تحديد طلب السعر كمعالَج بنجاح.');
    }
}
