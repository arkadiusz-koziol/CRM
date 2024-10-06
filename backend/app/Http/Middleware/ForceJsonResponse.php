<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        try {
            return $next($request);

        } catch (\Throwable $exception) {
            return $this->handleException($request, $exception);
        }
    }

    protected function handleException(Request $request, $exception): JsonResponse
    {
        // Handle validation errors
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }

        // Handle authorization errors (403 Forbidden)
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => 'Forbidden: You don\'t have permission to access this resource.'
            ], 403);
        }

        // Handle authentication errors (401 Unauthenticated)
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated: Please log in to access this resource.'
            ], 401);
        }

        // Handle resource not found errors (404 Not Found)
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Resource not found.'
            ], 404);
        }

        // Handle general HTTP exceptions
        if ($exception instanceof HttpException) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'An error occurred.',
            ], $exception->getStatusCode());
        }

        // Handle other types of exceptions (500 Internal Server Error by default)
        return response()->json([
            'message' => 'An internal server error occurred.',
            'exception' => get_class($exception),
            'trace' => config('app.debug') ? $exception->getTrace() : null,
        ], 500);
    }
}
