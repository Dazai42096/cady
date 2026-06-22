<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /**
     * Show reset form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle password reset.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.exists' => 'البريد الإلكتروني غير مسجل في النظام.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.'
        ]);

        try {
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

            if (!$resetRecord) {
                return back()->withErrors(['email' => 'رابط إعادة التعيين هذا غير صالح أو منتهي الصلاحية.']);
            }

            // Update user password
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Delete reset token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Log audit log
            app(\App\Services\AuditLogService::class)->log(
                action: 'auth.password_reset',
                metadata: ['user_id' => $user->id, 'email' => $user->email]
            );

            return redirect()->route('login')->with('success', 'تمت إعادة تعيين كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.');

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'حدث خطأ أثناء إعادة تعيين كلمة المرور. يرجى المحاولة لاحقاً.']);
        }
    }
}
