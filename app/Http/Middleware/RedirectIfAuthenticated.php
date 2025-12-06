<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
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
            if (auth()->guard($guard)->check()) {
                return redirect($this->redirectPath($request));
            }
        }

        return $next($request);
    }

    /**
     * Get the redirect path for authenticated users.
     */
    protected function redirectPath(Request $request): string
    {
        $user = $request->user();

        if (! $user) {
            return route('organization.select');
        }

        // If on subdomain, redirect to that subdomain's dashboard
        if ($organizationSlug = $request->route('organizationSlug')) {
            return route('dashboard', ['organizationSlug' => $organizationSlug]);
        }

        // If user has a current organization, redirect to their subdomain
        if ($user->current_organization_id) {
            $organization = $user->organizations()->find($user->current_organization_id);

            if ($organization) {
                $protocol = $request->secure() ? 'https' : 'http';
                $mainDomain = config('app.main_domain');

                return "{$protocol}://{$organization->slug}.{$mainDomain}/dashboard";
            }
        }

        // Otherwise, show organization selection
        return route('organization.select');
    }
}
