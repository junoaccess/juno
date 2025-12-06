<?php

namespace App\Models\Traits;

use App\Models\Scopes\OrganizationScope;

trait OrganizationScoped
{
    /**
     * Boot the trait and apply the global scope.
     */
    protected static function bootOrganizationScoped(): void
    {
        static::addGlobalScope(new OrganizationScope);
    }
}
