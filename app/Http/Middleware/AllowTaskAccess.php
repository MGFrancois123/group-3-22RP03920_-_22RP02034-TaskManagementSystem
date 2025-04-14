<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowTaskAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Allow all authenticated users to access tasks
        if ($request->user()) {
            return $next($request);
        }

        return redirect('login');
    }
}
