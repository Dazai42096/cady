<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.exists' => 'البريد الإلكتروني غير مسجل في المنظومة.'
        ]);

        try {
            $token = Str::random(64);

            // Save reset token in DB
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $token,
                    'created_at' => now()
                ]
            );

            $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

            // For development, if mail isn't configured, we log the reset link to storage/logs/laravel.log
            // and we can also send it using Laravel's Mail facility if configured.
            \Illuminate\Support\Facades\Log::info("Password reset link for {$request->email}: {$resetUrl}");

            // Attempt to send email, catch exception silently if SMTP not configured yet
            try {
                Mail::raw("رابط إعادة تعيين كلمة المرور الخاص بك هو: {$resetUrl}", function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject("إعادة تعيين كلمة المرور - CADY EST");
                });
            } catch (\Exception $e) {
                // Ignore email failure in development/local environments
            }

            return back()->with('status', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني بنجاح (يرجى مراجعة البريد أو سجلات النظام).');

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'حدث خطأ أثناء إرسال الرابط. يرجى المحاولة لاحقاً.']);
        }
    }
}
