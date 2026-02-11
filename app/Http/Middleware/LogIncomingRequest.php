<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogIncomingRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Incoming request', [
            'method' => $request->method(),
            'path' => $request->path(),
            'uri' => $request->getRequestUri(),
        ]);

        return $next($request);
    }
}
