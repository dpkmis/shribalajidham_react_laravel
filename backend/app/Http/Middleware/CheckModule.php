<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $module
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $module)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super admin bypasses all checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user can access the module
        if (!$user->canAccessModule($module)) {
            $user->logActivity(
                'unauthorized_module_access',
                $module,
                "Attempted to access module: {$module}"
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to access this module.'
                ], 403);
            }

            abort(403, 'You do not have permission to access this module.');
        }

        return $next($request);
    }
}

