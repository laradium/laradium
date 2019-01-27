<?php

namespace Laradium\Laradium\Http\Middleware;

use Closure;

class LaradiumMiddleware
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
        $user = auth()->user();

        if (!$user) {
            return redirect('/admin/login');
        }

        if (!$user->is_admin) {
            return redirect('/admin/login');
        }

        if (!laradium()->hasPermissionTo($user)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Access denied'
                ], 403);
            }

            return redirect('/admin/access-denied');
        }

        return $next($request);
    }
}
