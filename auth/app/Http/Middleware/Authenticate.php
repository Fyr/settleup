<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * Create a new filter instance.
     *
     * @return void
     */
    public function __construct(protected Guard $auth)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
