<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class ForceJsonResponse extends Middleware
{
    /**
     * Handle an incoming request.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API routes, return null to prevent redirect
        if ($request->is('api/*')) {
            return null;
        }

        // For web routes, redirect to login
        return route('login');
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards): void
    {
        if ($request->is('api/*')) {
            abort(401, 'Unauthenticated.');
        }

        parent::unauthenticated($request, $guards);
    }
}
