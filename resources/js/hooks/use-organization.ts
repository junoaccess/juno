import { usePage } from '@inertiajs/react';

interface Organization {
    id: number;
    name: string;
    slug: string;
}

/**
 * Hook to access the current organization context.
 * Returns the organization data and a slug helper for route building.
 */
export function useOrganization() {
    const { organization } = usePage<{
        organization?: Organization;
    }>().props;

    return {
        organization,
        slug: organization?.slug || '',
        hasOrganization: !!organization,
    };
}
