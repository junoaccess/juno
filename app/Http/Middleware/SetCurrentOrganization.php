<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Support\CurrentOrganization;
use Illuminate\Http\Request;
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
        $currentOrganization = app(CurrentOrganization::class);

        // Extract organization slug from route parameter (subdomain routing)
        $organizationSlug = $request->route('organizationSlug');

        if ($organizationSlug) {
            // Resolve organization by slug from subdomain
            $organization = Organization::where('slug', $organizationSlug)->first();

            if (! $organization) {
                abort(404, 'Organization not found');
            }

            // Set the current organization in the context
            $currentOrganization->set($organization);

            // Make it available in the request
            $request->merge(['currentOrganization' => $organization]);
        }

        return $next($request);
    }
}
