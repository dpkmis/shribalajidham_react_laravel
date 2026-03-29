<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();


        // dd($user->isSuperAdmin());

        // Super admin bypasses all permission checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has the required permission
        if (!$user->hasPermission($permission)) {
            // Log unauthorized access attempt
            $user->logActivity(
                'unauthorized_access_attempt',
                null,
                "Attempted to access: {$permission}"
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            }

            abort(403, 'Unauthorized action. You do not have the required permission.');
        }

        return $next($request);
    }
}
