<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Example usage:
     * role:admin
     * role:admin,sales
     * role:admin,sales,support
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'You must be logged in.');
        }

        if (!$user->is_active) {
            auth()->logout();

            abort(403, 'Your account is inactive.');
        }

        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            auth()->logout();

            abort(403, 'Your account is temporarily locked.');
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}