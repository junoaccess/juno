<?php

use App\Support\CurrentOrganization;

if (! function_exists('current_organization')) {
    /**
     * Get the current organization instance.
     */
    function current_organization(): CurrentOrganization
    {
        return app(CurrentOrganization::class);
    }
}
