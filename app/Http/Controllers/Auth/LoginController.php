<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // 1. Verify credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Log failed login
            $this->auditLogService->log(
                action: 'auth.login_failed',
                metadata: ['email' => $request->email, 'reason' => 'invalid_credentials']
            );

            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
            ]);
        }

        // 2. Verify active status
        if (!$user->is_active) {
            // Log inactive login attempt
            $this->auditLogService->log(
                action: 'auth.login_failed',
                user_id: $user->id,
                metadata: ['email' => $request->email, 'reason' => 'account_inactive']
            );

            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'هذا الحساب غير نشط حالياً أو بانتظار موافقة الإدارة. يرجى التواصل مع الدعم الفني.',
            ]);
        }

        // 3. Login user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Log successful login
        $this->auditLogService->log(
            action: 'auth.login_success',
            metadata: ['user_id' => $user->id, 'email' => $user->email]
        );

        return $this->redirectUser($user);
    }

    /**
     * Log out the user.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Log logout event
            $this->auditLogService->log(
                action: 'auth.logout',
                metadata: ['user_id' => $user->id, 'email' => $user->email]
            );

            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * Helper to redirect user by role.
     */
    protected function redirectUser(User $user)
    {
        if ($user->isStaff()) {
            return redirect()->intended('/dashboard');
        }

        if ($user->isCustomer()) {
            return redirect()->intended('/portal');
        }

        Auth::logout();
        return redirect()->route('login')->withErrors(['email' => 'دور المستخدم غير معرف في النظام.']);
    }
}
