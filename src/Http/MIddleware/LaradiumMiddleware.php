<?php

namespace Laradium\Laradium\Http\Middleware;

use Closure;

class LaradiumMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        auth()->shouldUse('admin');

        $user = auth()->user();
        if (!$user) {
            return redirect('/admin/login');
        }

        if (!$user->is_admin) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
