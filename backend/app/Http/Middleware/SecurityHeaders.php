<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Cache headers for API responses
        if ($request->is('api/v1/public/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=600');
            $response->headers->set('Vary', 'Accept, Accept-Encoding');
        }

        // Cache headers for static storage files (images, uploads)
        if ($request->is('storage/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000'); // 30 days
        }

        return $response;
    }
}
