import { DataTable } from '@/components/data-table/data-table';
import {
    LaravelPagination,
    PaginationLink,
} from '@/components/pagination/laravel-pagination';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { create } from '@/routes/organizations';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import {
    Organization,
    organizationsColumns,
} from './components/organisations-columns';

interface OrganizationsIndexProps {
    organizations: {
        data: Organization[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number | null;
        to: number | null;
        path: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organizations',
        href: '/organizations',
    },
];

export default function OrganizationsIndex({
    organizations,
}: OrganizationsIndexProps) {
    const handleCreateOrganization = () => {
        router.visit(create.url());
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Organizations" />

            <div className="flex flex-col gap-6 p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Organizations
                        </h1>
                        <p className="text-muted-foreground">
                            Manage organizations and their settings.
                        </p>
                    </div>
                    <Button onClick={handleCreateOrganization}>
                        <PlusIcon />
                        Add Organization
                    </Button>
                </div>

                {/* Data Table */}
                <DataTable
                    columns={organizationsColumns}
                    data={organizations.data}
                    searchable={true}
                    searchPlaceholder="Search organizations..."
                    enableColumnVisibility={true}
                    enableSorting={true}
                />

                {/* Pagination */}
                <LaravelPagination
                    meta={{
                        current_page: organizations.current_page,
                        from: organizations.from,
                        last_page: organizations.last_page,
                        links: organizations.links,
                        path: organizations.path,
                        per_page: organizations.per_page,
                        to: organizations.to,
                        total: organizations.total,
                    }}
                    only={['organizations']}
                />
            </div>
        </AppLayout>
    );
}
