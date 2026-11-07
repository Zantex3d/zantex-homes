<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('tenant')) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
