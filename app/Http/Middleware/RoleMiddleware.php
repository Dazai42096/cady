<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Convert enums or strings to string comparison
        $userRole = $request->user()->role->value ?? $request->user()->role;
        
        if (!in_array($userRole, $roles)) {
            abort(403, 'غير مصرح لك بالدخول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
