<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserOrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $orgId = $this->resolveCurrentOrganizationId();

        if ($orgId) {
            $builder->whereHas('organizations', function ($query) use ($orgId) {
                $query->where('organizations.id', $orgId);
            });
        }
    }

    /**
     * Resolve the current organization ID from various sources.
     */
    protected function resolveCurrentOrganizationId(): ?int
    {
        // 1. From container binding (for testing/console)
        if (app()->bound('currentOrganization')) {
            $org = app('currentOrganization');

            return $org?->id ?? null;
        }

        if (app()->bound('currentOrganizationId')) {
            return app('currentOrganizationId');
        }

        // 2. From session
        if (session()->has('current_organization_id')) {
            return session('current_organization_id');
        }

        // 3. From authenticated user
        if (auth()->check() && auth()->user()->current_organization_id) {
            return auth()->user()->current_organization_id;
        }

        return null;
    }
}
