<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\VisitStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\MaintenanceVisit;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function __construct(private AuditLogService $audit) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', MaintenanceVisit::class);

        $visits = MaintenanceVisit::with(['contract.customer', 'contract.generator', 'technician'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->technician_id, fn($q) => $q->where('assigned_to', $request->technician_id))
            ->orderBy('planned_date')
            ->paginate(20)
            ->withQueryString();

        $technicians = User::whereIn('role', ['admin', 'support'])->orderBy('name')->get();

        return view('dashboard.visits.index', compact('visits', 'technicians'));
    }

    public function show(MaintenanceVisit $visit)
    {
        $this->authorize('view', $visit);
        $visit->load(['contract.customer', 'contract.generator', 'technician']);
        return view('dashboard.visits.show', compact('visit'));
    }

    public function edit(MaintenanceVisit $visit)
    {
        $this->authorize('update', $visit);
        $visit->load(['contract.customer', 'contract.generator']);
        $technicians = User::whereIn('role', ['admin', 'support'])->orderBy('name')->get();
        return view('dashboard.visits.edit', compact('visit', 'technicians'));
    }

    public function update(UpdateVisitRequest $request, MaintenanceVisit $visit)
    {
        $this->authorize('update', $visit);

        DB::transaction(function () use ($request, $visit) {
            $visit->update($request->validated());

            $this->audit->log(
                action: 'visit_updated',
                entityType: MaintenanceVisit::class,
                entityId: $visit->id,
                newValues: $request->validated()
            );
        });

        return redirect()->route('dashboard.visits.show', $visit)
            ->with('success', 'تم تحديث بيانات الزيارة بنجاح');
    }

    public function confirm(MaintenanceVisit $visit)
    {
        $this->authorize('updateStatus', $visit);

        if ($visit->status !== VisitStatus::SCHEDULED) {
            return back()->with('error', 'يمكن تأكيد الزيارات المجدولة فقط');
        }

        $visit->update([
            'status'         => VisitStatus::CONFIRMED,
            'confirmed_date' => now()->toDateString(),
        ]);

        $this->audit->log('visit_confirmed', entityType: MaintenanceVisit::class, entityId: $visit->id);

        return back()->with('success', 'تم تأكيد الزيارة');
    }

    public function start(MaintenanceVisit $visit)
    {
        $this->authorize('updateStatus', $visit);

        if (!in_array($visit->status, [VisitStatus::SCHEDULED, VisitStatus::CONFIRMED])) {
            return back()->with('error', 'لا يمكن بدء هذه الزيارة في وضعها الحالي');
        }

        $visit->update(['status' => VisitStatus::IN_PROGRESS, 'actual_date' => now()->toDateString()]);
        $this->audit->log('visit_started', entityType: MaintenanceVisit::class, entityId: $visit->id);

        return back()->with('success', 'تم بدء الزيارة الميدانية');
    }

    public function complete(MaintenanceVisit $visit)
    {
        $this->authorize('updateStatus', $visit);

        if ($visit->status !== VisitStatus::IN_PROGRESS) {
            return back()->with('error', 'يمكن إتمام الزيارات الجارية فقط');
        }

        $visit->update(['status' => VisitStatus::COMPLETED]);
        $this->audit->log('visit_completed', entityType: MaintenanceVisit::class, entityId: $visit->id);

        return back()->with('success', 'تم إتمام الزيارة بنجاح');
    }

    public function cancel(MaintenanceVisit $visit)
    {
        $this->authorize('updateStatus', $visit);

        if ($visit->status === VisitStatus::COMPLETED) {
            return back()->with('error', 'لا يمكن إلغاء زيارة مكتملة');
        }

        $visit->update(['status' => VisitStatus::CANCELLED]);
        $this->audit->log('visit_cancelled', entityType: MaintenanceVisit::class, entityId: $visit->id);

        return back()->with('success', 'تم إلغاء الزيارة');
    }
}
