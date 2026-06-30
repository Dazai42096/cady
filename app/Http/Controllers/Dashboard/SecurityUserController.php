<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SecurityUserController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request)
    {
        $query = User::query()
            ->orderBy('role')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('dashboard.security-users.index', [
            'users' => $users,
        ]);
    }

    public function unlock(Request $request, User $user)
    {
        $oldValues = [
            'failed_login_attempts' => $user->failed_login_attempts,
            'failed_login_window_started_at' => $user->failed_login_window_started_at,
            'locked_until' => $user->locked_until,
        ];

        $user->forceFill([
            'failed_login_attempts' => 0,
            'failed_login_window_started_at' => null,
            'locked_until' => null,
        ])->save();

        $this->log($request, 'admin.user_unlock', $user, $oldValues, [
            'failed_login_attempts' => 0,
            'failed_login_window_started_at' => null,
            'locked_until' => null,
        ]);

        return back()->with('success', "Account unlocked for {$user->email}.");
    }

    public function resetTwoFactor(Request $request, User $user)
    {
        $oldValues = [
            'two_factor_enabled' => $user->two_factor_enabled,
            'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
        ];

        $user->forceFill([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->log($request, 'admin.user_reset_2fa', $user, $oldValues, [
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
        ]);

        return back()->with('success', "Two-factor authentication reset for {$user->email}.");
    }

    public function activate(Request $request, User $user)
    {
        $oldValues = [
            'is_active' => $user->is_active,
        ];

        $user->forceFill([
            'is_active' => true,
            'failed_login_attempts' => 0,
            'failed_login_window_started_at' => null,
            'locked_until' => null,
        ])->save();

        $this->log($request, 'admin.user_activate', $user, $oldValues, [
            'is_active' => true,
        ]);

        return back()->with('success', "Account activated for {$user->email}.");
    }

    public function deactivate(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return back()->withErrors([
                'user' => 'You cannot deactivate your own admin account.',
            ]);
        }

        $oldValues = [
            'is_active' => $user->is_active,
        ];

        $user->forceFill([
            'is_active' => false,
        ])->save();

        $this->log($request, 'admin.user_deactivate', $user, $oldValues, [
            'is_active' => false,
        ]);

        return back()->with('success', "Account deactivated for {$user->email}.");
    }

    private function log(Request $request, string $action, User $targetUser, array $oldValues, array $newValues): void
    {
        try {
            $this->auditLogService->log(
                action: $action,
                entityType: User::class,
                entityId: $targetUser->id,
                oldValues: $oldValues,
                newValues: array_merge($newValues, [
                    'target_email' => $targetUser->email,
                    'admin_email' => $request->user()?->email,
                    'ip' => $request->ip(),
                ])
            );
        } catch (\Throwable) {
            //
        }
    }
}