<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ServiceReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ServiceReportController extends Controller
{
    public function index(Request $request)
    {
        $customerIds = $this->customerIdsForUser($request);

        $reports = ServiceReport::with(['customer', 'generator', 'rental', 'maintenanceContract'])
            ->whereIn('customer_id', $customerIds)
            ->where('customer_visible', true)
            ->whereIn('status', ['submitted', 'approved'])
            ->latest()
            ->paginate(15);

        return view('portal.service-reports.index', compact('reports'));
    }

    public function show(Request $request, ServiceReport $serviceReport)
    {
        $report = $this->scopedReport($request, $serviceReport);
        $report->load(['customer', 'generator', 'rental', 'maintenanceContract', 'creator']);

        return view('portal.service-reports.show', compact('report'));
    }

    public function downloadPdf(Request $request, ServiceReport $serviceReport)
    {
        $report = $this->scopedReport($request, $serviceReport);
        $report->load(['customer', 'generator', 'rental', 'maintenanceContract', 'creator']);

        return Pdf::loadView('reports.service-report-pdf', [
            'report' => $report,
        ])->download($report->report_number . '.pdf');
    }

    private function scopedReport(Request $request, ServiceReport $serviceReport): ServiceReport
    {
        return ServiceReport::whereIn('customer_id', $this->customerIdsForUser($request))
            ->where('customer_visible', true)
            ->whereIn('status', ['submitted', 'approved'])
            ->whereKey($serviceReport->id)
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