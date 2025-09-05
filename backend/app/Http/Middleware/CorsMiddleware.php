<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'http://localhost:5173',
            'http://127.0.0.1:5173',
        ];

        $origin = $request->header('Origin');

        if (in_array($origin, $allowedOrigins, true)) {
            $response = $next($request);

            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set(
                'Access-Control-Allow-Headers',
                'Content-Type, Authorization, X-Requested-With, Accept, Origin, Cache-Control, Expires, Pragma'
            );
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');

            return $response;
        }

        // Handle preflight requests
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
            $response->headers->set('Access-Control-Allow-Origin', $origin ?? '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set(
                'Access-Control-Allow-Headers',
                'Content-Type, Authorization, X-Requested-With, Accept, Origin, Cache-Control, Expires, Pragma'
            );
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');

            return $response;
        }

        return $next($request);
    }
}
