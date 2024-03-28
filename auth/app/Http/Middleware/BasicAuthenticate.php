<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BasicAuthenticate implements AuthenticatesRequests
{
    final public const NO_AUTH_HEADER = 'Authentication header is missing';
    final public const INVALID_CREDENTIALS = 'Invalid credentials';

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // debug headers
        foreach ($_SERVER as $key => $value) {
            if (str_contains($key, 'HTTP')) {
                Log::debug("$key = $value");
            }
        }

        if (!$request->header('authorization')) {
            Log::debug(self::NO_AUTH_HEADER);

            return response(self::NO_AUTH_HEADER, 401);
        }
        if (Auth::check()) {
            return $next($request);
        }

        return response(self::INVALID_CREDENTIALS, 401);
    }
}
