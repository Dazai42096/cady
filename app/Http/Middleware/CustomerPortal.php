<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerPortal
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Must have customer role and be active
        if (!$user->isCustomer() || !$user->is_active) {
            abort(403, 'غير مصرح لك بالدخول لبوابة العملاء.');
        }

        // Must be linked to a customer company
        $customer = $user->customers()->where('status', \App\Enums\CustomerStatus::ACTIVE)->first();
        
        if (!$customer) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'حساب العميل الخاص بك غير مرتبط بأي شركة نشطة حالياً. يرجى التواصل مع الإدارة.',
            ]);
        }

        // Share the customer entity via request attributes
        $request->attributes->set('customer', $customer);

        return $next($request);
    }
}
