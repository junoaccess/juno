import { DataTable } from '@/components/data-table/data-table';
import {
    LaravelPagination,
    PaginationLink,
} from '@/components/pagination/laravel-pagination';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { create } from '@/routes/users';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import { User, usersColumns } from './components/users-columns';

interface UsersIndexProps {
    users: {
        data: User[];
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
        title: 'Users',
        href: '/users',
    },
];

export default function UsersIndex({ users }: UsersIndexProps) {
    const handleSearch = (value: string) => {
        // You can add server-side search by making an Inertia request
        // router.get('/users', { search: value }, { preserveState: true });
        console.log('Searching for:', value);
    };

    const handleCreateUser = () => {
        router.visit(create.url());
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />

            <div className="flex flex-col gap-6 p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Users
                        </h1>
                        <p className="text-muted-foreground">
                            Manage users and their access to your organization.
                        </p>
                    </div>
                    <Button onClick={handleCreateUser}>
                        <PlusIcon />
                        Add User
                    </Button>
                </div>

                {/* Data Table */}
                <DataTable
                    columns={usersColumns}
                    data={users.data}
                    searchable={true}
                    searchPlaceholder="Search users..."
                    onSearch={handleSearch}
                    enableColumnVisibility={true}
                    enableSorting={true}
                />

                {/* Pagination */}
                <LaravelPagination
                    meta={{
                        current_page: users.current_page,
                        from: users.from,
                        last_page: users.last_page,
                        links: users.links,
                        path: users.path,
                        per_page: users.per_page,
                        to: users.to,
                        total: users.total,
                    }}
                    only={['users']}
                />
            </div>
        </AppLayout>
    );
}
