<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $remember = $request->boolean('remember');

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && $user->isLocked()) {
            $this->logAuthEvent($request, 'auth.login_blocked_locked', $user);

            throw ValidationException::withMessages([
                'email' => 'Your account is temporarily locked. Please try again later or contact the administrator.',
            ]);
        }

        if (!Auth::attempt(['email' => $email, 'password' => $credentials['password']], $remember)) {
            if ($user) {
                $this->recordFailedLogin($request, $user);
            }

            throw ValidationException::withMessages([
                'email' => 'The provided email or password is incorrect.',
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            $this->logAuthEvent($request, 'auth.login_blocked_inactive', $user);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Your account is inactive. Please contact the administrator.',
            ]);
        }

        if ($user->isLocked()) {
            $this->logAuthEvent($request, 'auth.login_blocked_locked', $user);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Your account is temporarily locked. Please try again later.',
            ]);
        }

        $user->forceFill([
            'failed_login_attempts' => 0,
            'failed_login_window_started_at' => null,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        $this->logAuthEvent($request, 'auth.login_success', $user);

        if ($user->hasTwoFactorEnabled()) {
            session([
                'two_factor_user_id' => $user->id,
                'two_factor_remember' => $remember,
            ]);

            Auth::logout();

            return redirect()->route('two-factor.challenge');
        }

        return redirect()->route($this->redirectRouteForRole($user->role));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $this->logAuthEvent($request, 'auth.logout', $user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function recordFailedLogin(Request $request, User $user): void
    {
        $windowStartedAt = $user->failed_login_window_started_at;

        if (!$windowStartedAt || now()->diffInMinutes($windowStartedAt) >= 15) {
            $user->failed_login_attempts = 0;
            $user->failed_login_window_started_at = now();
        }

        $user->failed_login_attempts = (int) $user->failed_login_attempts + 1;

        if ($user->failed_login_attempts >= 5) {
            $user->locked_until = now()->addMinutes(30);

            $this->logAuthEvent($request, 'auth.account_locked_failed_attempts', $user);
        } else {
            $this->logAuthEvent($request, 'auth.login_failed', $user);
        }

        $user->save();
    }

    private function logAuthEvent(Request $request, string $action, User $user): void
    {
        try {
            $this->auditLogService->log(
                action: $action,
                entityType: User::class,
                entityId: $user->id,
                newValues: [
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                ]
            );
        } catch (\Throwable) {
            // Login/logout must not fail if audit logging fails.
        }
    }

    private function redirectRouteForRole(?string $role): string
    {
        return match ($role) {
            'admin', 'sales', 'support' => 'dashboard.index',
            'customer' => 'portal.index',
            default => 'login',
        };
    }
}