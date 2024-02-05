<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * Check if the request has a valid Bearer token.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $expectedToken = 'SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=';
        $authorizationHeader = $request->header('Bearer');

        if (!$authorizationHeader) {
            return response()->json(['message' => 'Authorization header is missing.'], 401);
        }

        if ($authorizationHeader !== $expectedToken) {
            return response()->json(['message' => 'Unauthorized access.'], 401);
        }

        return $next($request);
    }
}
