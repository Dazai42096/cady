<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:12',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&^_\-+=.,:;~()[\]{}]/',
            ],
        ], [
            'password.min' => 'Password must be at least 12 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain uppercase, lowercase, number, and symbol.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'failed_login_attempts' => 0,
                    'failed_login_window_started_at' => null,
                    'locked_until' => null,
                ])->save();

                $this->logPasswordReset($request, $user);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return redirect()->route('login')->with('success', 'Password reset successfully. You can now log in.');
    }

    private function logPasswordReset(Request $request, User $user): void
    {
        try {
            $this->auditLogService->log(
                action: 'auth.password_reset',
                entityType: User::class,
                entityId: $user->id,
                newValues: [
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                ]
            );
        } catch (\Throwable) {
            // Password reset must not fail if audit logging fails.
        }
    }
}