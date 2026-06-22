<?php

namespace App\Http\Controllers\Portal;

use App\Enums\ContractStatus;
use App\Enums\QuotationStatus;
use App\Enums\VisitStatus;
use App\Http\Controllers\Controller;
use App\Models\MaintenanceContract;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PortalController extends Controller
{
    /**
     * Resolve the active customer for the authenticated portal user.
     * The CustomerPortal middleware already validated and set this.
     */
    private function resolveCustomer(): \App\Models\Customer
    {
        return request()->attributes->get('customer');
    }

    /**
     * Customer Portal Dashboard — shows overview stats, generators,
     * active contracts, recent quotations, and upcoming visits.
     */
    public function index()
    {
        $customer = $this->resolveCustomer();

        // Eager-load generators
        $customer->load([
            'generators',
            'maintenanceContracts' => fn ($q) => $q->where('status', ContractStatus::ACTIVE)
                                                    ->with(['visits' => fn ($vq) => $vq
                                                        ->whereIn('status', [VisitStatus::SCHEDULED, VisitStatus::CONFIRMED])
                                                        ->orderBy('planned_date')
                                                        ->limit(5)
                                                    ]),
            'quotations' => fn ($q) => $q->latest()->limit(5),
        ]);

        // Stats
        $generatorsCount       = $customer->generators->count();
        $activeContractsCount  = $customer->maintenanceContracts->count();
        $sentQuotationsCount   = $customer->quotations()
                                          ->whereIn('status', [QuotationStatus::SENT, QuotationStatus::ACCEPTED])
                                          ->count();

        // Upcoming visits (next 3, across all contracts)
        $upcomingVisits = collect();
        foreach ($customer->maintenanceContracts as $contract) {
            $upcomingVisits = $upcomingVisits->merge($contract->visits);
        }
        $upcomingVisits = $upcomingVisits
            ->sortBy('planned_date')
            ->take(3)
            ->values();

        return view('portal.index', compact(
            'customer',
            'generatorsCount',
            'activeContractsCount',
            'sentQuotationsCount',
            'upcomingVisits',
        ));
    }

    /**
     * Customer's own quotations list (paginated).
     */
    public function quotations()
    {
        $customer   = $this->resolveCustomer();
        $quotations = $customer->quotations()->latest()->paginate(15);

        return view('portal.quotations', compact('customer', 'quotations'));
    }

    /**
     * Customer's own maintenance contracts list (paginated).
     */
    public function contracts()
    {
        $customer  = $this->resolveCustomer();
        $contracts = $customer->maintenanceContracts()->latest()->paginate(15);

        return view('portal.contracts', compact('customer', 'contracts'));
    }

    /**
     * Download a quotation PDF — validates that the quotation belongs
     * to the authenticated customer before generating the PDF.
     */
    public function downloadQuotationPdf(Quotation $quotation)
    {
        $customer = $this->resolveCustomer();

        // Security: ensure this quotation belongs to this customer
        abort_unless(
            $quotation->customer_id === $customer->id,
            403,
            'ليس لديك صلاحية لتنزيل هذه الوثيقة.'
        );

        $quotation->load(['customer', 'items', 'creator']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.quotation', compact('quotation'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('quotation-' . $quotation->ref_number . '.pdf');
    }

    /**
     * Download a maintenance contract PDF — validates that the contract
     * belongs to the authenticated customer before generating.
     */
    public function downloadContractPdf(MaintenanceContract $contract)
    {
        $customer = $this->resolveCustomer();

        // Security: ensure this contract belongs to this customer
        abort_unless(
            $contract->customer_id === $customer->id,
            403,
            'ليس لديك صلاحية لتنزيل هذه الوثيقة.'
        );

        $contract->load(['customer', 'generator', 'creator', 'visits']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.contract', compact('contract'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('contract-' . $contract->ref_number . '.pdf');
    }
}
