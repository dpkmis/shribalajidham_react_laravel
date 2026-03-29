<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if account is locked
        if ($user->is_locked) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is temporarily locked. Please contact administrator.'
                ], 423);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is temporarily locked. Please contact administrator.']);
        }

        // Check if account is inactive
        if (!$user->is_active) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account has been deactivated. Please contact administrator.'
                ], 403);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact administrator.']);
        }

        return $next($request);
    }
}
