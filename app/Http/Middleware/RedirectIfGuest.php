<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (! auth()->guard($guard)->check()) {
                return redirect($this->redirectPath($request));
            }
        }

        return $next($request);
    }

    /**
     * Get the redirect path for guests.
     */
    protected function redirectPath(Request $request): string
    {
        // If on subdomain, redirect to subdomain login
        if ($request->route('organizationSlug')) {
            return route('login');
        }

        // Otherwise redirect to root domain organization selection
        return route('organization.select');
    }
}
