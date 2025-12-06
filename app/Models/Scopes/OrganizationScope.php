<?php

namespace App\Models\Scopes;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Cache;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $orgId = $this->resolveOrganizationId();

        if ($orgId === null) {
            return;
        }

        $builder->where($model->getTable().'.organization_id', $orgId);
    }

    /**
     * Resolve current organization ID from the request domain slug.
     */
    protected function resolveOrganizationId(): ?int
    {
        // 1) Container binding override (for testing or console commands)
        if (app()->bound('currentOrganizationId')) {
            return (int) app('currentOrganizationId');
        }

        // 2) Resolve from request domain/subdomain slug
        $slug = $this->extractOrganizationSlug();

        if (! $slug) {
            return null;
        }

        // Cache the organization lookup to avoid repeated queries
        return Cache::remember(
            "org_id_by_slug:{$slug}",
            now()->addMinutes(60),
            fn () => Organization::withoutGlobalScopes()->where('slug', $slug)->value('id')
        );
    }

    /**
     * Extract organization slug from the current request domain/subdomain.
     */
    protected function extractOrganizationSlug(): ?string
    {
        if (! app()->bound('request')) {
            return null;
        }

        $request = app('request');
        $host = $request->getHost();

        // Option 1: Subdomain-based (e.g., acme.yourdomain.com)
        // Extract first subdomain segment as organization slug
        $parts = explode('.', $host);
        if (count($parts) >= 3) {
            // Assuming format: {slug}.{domain}.{tld}
            return $parts[0];
        }

        // Option 2: Custom domain (e.g., acme.com maps to organization)
        // You could look up custom domains in a separate table
        // For now, if it's a single or two-part domain, return the first part
        if (count($parts) >= 2) {
            return $parts[0];
        }

        return null;
    }

    /**
     * Extend the query builder with the custom methods.
     */
    public function extend(Builder $builder)
    {
        // no custom macros for now
    }
}
