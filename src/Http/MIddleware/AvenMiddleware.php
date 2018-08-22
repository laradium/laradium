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
            return redirect('/admin/login');
        }
        if(auth()->check() && !auth()->user()->is_admin) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
