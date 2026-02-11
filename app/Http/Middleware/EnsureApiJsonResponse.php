<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->is('api/*') && $response->isRedirection()) {
            return response()->json([
                'error' => 'redirect_intercepted',
                'location' => $response->headers->get('Location'),
                'status' => $response->getStatusCode(),
            ], 500);
        }

        return $response;
    }
}
