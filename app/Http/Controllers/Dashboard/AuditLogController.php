<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a paginated listing of all audit log entries.
     * Restricted to Admin role via AuditLogPolicy.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $logs = AuditLog::with('user')
            ->when(
                $request->search,
                fn ($q) => $q->where('action', 'like', "%{$request->search}%")
                             ->orWhere('description', 'like', "%{$request->search}%")
            )
            ->when(
                $request->user_id,
                fn ($q) => $q->where('user_id', $request->user_id)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Fetch staff users for filter dropdown (admins/sales/support only)
        $staffUsers = User::whereIn('role', ['admin', 'sales', 'support'])
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        return view('dashboard.audit-logs.index', compact('logs', 'staffUsers'));
    }
}
