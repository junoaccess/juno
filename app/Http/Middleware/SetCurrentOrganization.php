<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Get current organization from session or user's current_organization_id
        $currentOrgId = session('current_organization_id') ?? $user->current_organization_id;

        if ($currentOrgId) {
            // Verify user is still a member of this organization
            $organization = $user->organizations()->find($currentOrgId);

            if ($organization) {
                // Sync session and user model if they differ
                if ($user->current_organization_id !== $organization->id) {
                    $user->setCurrentOrganization($organization);
                }

                session(['current_organization_id' => $organization->id]);

                // Make organization available throughout the request
                $request->merge(['currentOrganization' => $organization]);
                app()->instance('currentOrganization', $organization);

                return $next($request);
            }
        }

        // No valid organization set - redirect to organization selection if user has orgs
        if ($user->organizations()->exists()) {
            if (! $request->routeIs('organizations.select') && ! $request->is('api/*')) {
                return redirect()->route('organizations.select');
            }
        }

        return $next($request);
    }
}
