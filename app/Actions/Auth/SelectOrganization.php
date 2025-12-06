<?php

namespace App\Actions\Auth;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SelectOrganization
{
    /**
     * Select an organization and return the subdomain URL.
     */
    public function handle(Request $request): string
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'alpha_dash', 'max:255'],
        ]);

        $organization = Organization::where('slug', $validated['slug'])->first();

        if (! $organization) {
            throw ValidationException::withMessages([
                'slug' => 'We could not find that organization.',
            ]);
        }

        return $this->buildSubdomainUrl($request, $organization);
    }

    /**
     * Build the subdomain URL for the organization.
     */
    protected function buildSubdomainUrl(Request $request, Organization $organization): string
    {
        $protocol = $request->secure() ? 'https' : 'http';
        $mainDomain = config('app.main_domain');

        return "{$protocol}://{$organization->slug}.{$mainDomain}/login";
    }
}
