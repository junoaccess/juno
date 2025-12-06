import { DataTableColumnHeader } from '@/components/data-table/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { destroy, edit } from '@/routes/organizations';
import { router } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import {
    BuildingIcon,
    MoreHorizontalIcon,
    PencilIcon,
    TrashIcon,
    UsersIcon,
} from 'lucide-react';

export interface Organization {
    id: number;
    name: string;
    slug: string;
    email: string | null;
    phone: string | null;
    website: string | null;
    owner_name: string | null;
    owner_email: string | null;
    owner_phone: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    users_count?: number;
    teams_count?: number;
}

export const organizationsColumns: ColumnDef<Organization>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Organization" />
        ),
        cell: ({ row }) => {
            const org = row.original;
            return (
                <div className="flex items-center gap-3">
                    <div className="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <BuildingIcon className="size-5" />
                    </div>
                    <div>
                        <div className="font-medium">{org.name}</div>
                        <div className="text-xs text-muted-foreground">
                            {org.slug}
                        </div>
                    </div>
                </div>
            );
        },
    },
    {
        accessorKey: 'owner_email',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Owner" />
        ),
        cell: ({ row }) => {
            const ownerName = row.original.owner_name;
            const ownerEmail = row.getValue('owner_email') as string | null;
            return (
                <div className="flex flex-col">
                    {ownerName && (
                        <span className="font-medium">{ownerName}</span>
                    )}
                    {ownerEmail && (
                        <span className="text-xs text-muted-foreground">
                            {ownerEmail}
                        </span>
                    )}
                    {!ownerName && !ownerEmail && (
                        <span className="text-muted-foreground">N/A</span>
                    )}
                </div>
            );
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Contact" />
        ),
        cell: ({ row }) => {
            const email = row.getValue('email') as string | null;
            const phone = row.original.phone;
            return (
                <div className="flex flex-col">
                    {email && <span className="text-sm">{email}</span>}
                    {phone && (
                        <span className="text-xs text-muted-foreground">
                            {phone}
                        </span>
                    )}
                    {!email && !phone && (
                        <span className="text-muted-foreground">N/A</span>
                    )}
                </div>
            );
        },
    },
    {
        accessorKey: 'users_count',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Members" />
        ),
        cell: ({ row }) => {
            const count = row.getValue('users_count') as number | undefined;
            return (
                <Badge variant="secondary" className="gap-1.5">
                    <UsersIcon className="size-3" />
                    {count ?? 0}
                </Badge>
            );
        },
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Created" />
        ),
        cell: ({ row }) => {
            const date = new Date(row.getValue('created_at'));
            return (
                <span className="text-muted-foreground">
                    {date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                    })}
                </span>
            );
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const org = row.original;

            return (
                <div className="flex justify-end">
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon-sm">
                                <MoreHorizontalIcon />
                                <span className="sr-only">Open menu</span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuItem
                                onClick={() => {
                                    navigator.clipboard.writeText(org.slug);
                                }}
                            >
                                Copy slug
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                onClick={() => {
                                    router.visit(edit.url(org.id));
                                }}
                            >
                                <PencilIcon />
                                Edit organization
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                variant="destructive"
                                onClick={() => {
                                    if (
                                        confirm(
                                            'Are you sure you want to delete this organization?',
                                        )
                                    ) {
                                        router.delete(destroy.url(org.id));
                                    }
                                }}
                            >
                                <TrashIcon />
                                Delete organization
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            );
        },
    },
];
