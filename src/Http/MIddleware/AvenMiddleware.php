<?php

namespace Netcore\Aven\Http\Middleware;

use Closure;

class AvenMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            abort(404);
        }
        if(auth()->check() && !auth()->user()->is_admin) {
            abort(404);
        }

        return $next($request);
    }
}
