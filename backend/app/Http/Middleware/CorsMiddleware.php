<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        
        // Get allowed origins from environment variable, with fallback to include production and localhost
        $corsOrigins = env('CORS_ALLOWED_ORIGINS', 'https://easypass.up.railway.app,http://localhost:5173,http://localhost:5174');
        $allowedOrigins = array_filter(explode(',', $corsOrigins));
        
        // Add development origins
        $allowedOrigins = array_merge($allowedOrigins, [
            'http://localhost:5173',
            'http://localhost:5174',
            'http://127.0.0.1:5173',
            'http://127.0.0.1:5174',
        ]);
        
        // Add production origins
        $allowedOrigins = array_merge($allowedOrigins, [
            'https://easypass.up.railway.app',
        ]);
        
        // Remove duplicates
        $allowedOrigins = array_unique($allowedOrigins);
        
        // Check if origin matches Railway pattern (for dynamic subdomains)
        $isRailwayOrigin = $origin && preg_match('/^https:\/\/.*\.up\.railway\.app$/', $origin);
        
        // Determine the origin to allow
        $allowOrigin = '*'; // Default to wildcard for development
        
        if ($origin && (in_array($origin, $allowedOrigins) || $isRailwayOrigin)) {
            $allowOrigin = $origin;
        } else if (!empty($allowedOrigins)) {
            $allowOrigin = $allowedOrigins[0];
        }

        // Handle preflight OPTIONS request
        if ($request->getMethod() === "OPTIONS") {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $allowOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
                ->header('Access-Control-Max-Age', '86400');
        }

        $response = $next($request);

        // Add CORS headers to all responses
        $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');

        return $response;
    }
}
