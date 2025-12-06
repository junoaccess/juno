<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordWithOrganization extends RequirePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $redirectToRoute
     * @param  string|null  $passwordTimeoutSeconds
     */
    public function handle($request, Closure $next, $redirectToRoute = null, $passwordTimeoutSeconds = null): Response
    {
        if ($this->shouldConfirmPassword($request, $passwordTimeoutSeconds)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Password confirmation required.'], 423);
            }

            // Get the organization slug from the route parameter
            $organizationSlug = $request->route('organizationSlug');

            // Generate the password confirmation URL with the organization slug
            return redirect()->guest(
                $this->urlGenerator->route($redirectToRoute ?: 'password.confirm', [
                    'organizationSlug' => $organizationSlug,
                ])
            );
        }

        return $next($request);
    }
}
