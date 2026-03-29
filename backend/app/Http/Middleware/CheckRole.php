<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            $user->logActivity(
                'unauthorized_access_attempt',
                null,
                "Attempted to access with required roles: " . implode(', ', $roles)
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have the required role to access this resource.'
                ], 403);
            }

            abort(403, 'Unauthorized action. Required role not found.');
        }

        return $next($request);
    }
}