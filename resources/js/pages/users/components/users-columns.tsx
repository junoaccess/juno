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
import { destroy, edit } from '@/routes/users';
import { router } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { MoreHorizontalIcon, PencilIcon, TrashIcon } from 'lucide-react';

export interface User {
    id: number;
    uid: string;
    first_name: string;
    last_name: string;
    middle_name: string | null;
    name: string;
    email: string;
    phone: string | null;
    date_of_birth: string | null;
    email_verified_at: string | null;
    profile_photo_path: string | null;
    profile_photo_url: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export const usersColumns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Name" />
        ),
        cell: ({ row }) => {
            const user = row.original;
            return (
                <div className="flex items-center gap-3">
                    {user.profile_photo_url ? (
                        <img
                            src={user.profile_photo_url}
                            alt={user.name}
                            className="size-8 rounded-full object-cover"
                        />
                    ) : (
                        <div className="flex size-8 items-center justify-center rounded-full bg-primary/10 text-sm font-medium text-primary">
                            {user.first_name[0]}
                            {user.last_name[0]}
                        </div>
                    )}
                    <div>
                        <div className="font-medium">{user.name}</div>
                        <div className="text-xs text-muted-foreground">
                            {user.uid}
                        </div>
                    </div>
                </div>
            );
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Email" />
        ),
        cell: ({ row }) => {
            const email = row.getValue('email') as string;
            const verified = row.original.email_verified_at !== null;
            return (
                <div className="flex items-center gap-2">
                    <span>{email}</span>
                    {verified && (
                        <Badge variant="secondary" className="text-xs">
                            Verified
                        </Badge>
                    )}
                </div>
            );
        },
    },
    {
        accessorKey: 'phone',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Phone" />
        ),
        cell: ({ row }) => {
            const phone = row.getValue('phone') as string | null;
            return (
                <span className="text-muted-foreground">{phone || 'N/A'}</span>
            );
        },
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Joined" />
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
            const user = row.original;

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
                                    navigator.clipboard.writeText(user.email);
                                }}
                            >
                                Copy email
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                onClick={() => {
                                    router.visit(edit.url(user.id));
                                }}
                            >
                                <PencilIcon />
                                Edit user
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                variant="destructive"
                                onClick={() => {
                                    if (
                                        confirm(
                                            'Are you sure you want to delete this user?',
                                        )
                                    ) {
                                        router.delete(destroy.url(user.id));
                                    }
                                }}
                            >
                                <TrashIcon />
                                Delete user
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            );
        },
    },
];
