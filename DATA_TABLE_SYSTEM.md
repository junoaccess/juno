# Data Table and Pagination System

This document describes the reusable data table and Laravel-compatible pagination system implemented for Juno.

## Table of Contents
- [Overview](#overview)
- [Existing shadcn/ui Components](#existing-shadcnui-components)
- [Data Table System](#data-table-system)
- [Pagination System](#pagination-system)
- [Usage Guide](#usage-guide)
- [Examples](#examples)

## Overview

The system provides:
- ✅ Generic, reusable data table component using **shadcn/ui** + **TanStack Table v8**
- ✅ Laravel-compatible pagination component that works with Laravel's standard paginators
- ✅ Reusable column definition modules following SOLID, DRY, and KISS principles
- ✅ Full TypeScript support with proper type definitions
- ✅ Kebab-case naming for all new files and directories

## Existing shadcn/ui Components

All components are located in `resources/js/components/ui/` and use kebab-case file naming.

### Forms
- `button.tsx` - Button component with variants (default, destructive, outline, secondary, ghost, link)
- `input.tsx` - Text input component
- `textarea.tsx` - Multi-line text input
- `select.tsx` - Select/dropdown input
- `checkbox.tsx` - Checkbox input
- `label.tsx` - Form label
- `switch.tsx` - Toggle switch
- `radio-group.tsx` - Radio button group
- `form.tsx` - Form wrapper with validation

### Data Display
- `table.tsx` - Table primitives (Table, TableHeader, TableBody, TableRow, TableCell, TableHead, TableFooter)
- `badge.tsx` - Badge/tag component
- `avatar.tsx` - User avatar component
- `card.tsx` - Card container component
- `separator.tsx` - Divider/separator line

### Navigation
- `dropdown-menu.tsx` - Dropdown menu component
- `navigation-menu.tsx` - Navigation menu
- `tabs.tsx` - Tabbed interface
- `breadcrumb.tsx` - Breadcrumb navigation
- `menubar.tsx` - Menu bar component

### Feedback
- `alert.tsx` - Alert/notification banner
- `alert-dialog.tsx` - Alert dialog modal
- `dialog.tsx` - Dialog/modal component
- `sheet.tsx` - Slide-out panel
- `drawer.tsx` - Drawer component
- `sonner.tsx` - Toast notifications
- `skeleton.tsx` - Loading skeleton
- `spinner.tsx` - Loading spinner
- `empty.tsx` - Empty state component

### Overlays
- `popover.tsx` - Popover component
- `tooltip.tsx` - Tooltip component
- `hover-card.tsx` - Hover card
- `context-menu.tsx` - Context menu

### Layout
- `sidebar.tsx` - Sidebar component
- `scroll-area.tsx` - Scrollable area
- `resizable.tsx` - Resizable panels
- `collapsible.tsx` - Collapsible section
- `accordion.tsx` - Accordion component
- `carousel.tsx` - Carousel/slider

### Other
- `calendar.tsx` - Date picker calendar
- `command.tsx` - Command palette
- `pagination.tsx` - Pagination primitives
- `progress.tsx` - Progress bar
- `slider.tsx` - Range slider
- `aspect-ratio.tsx` - Aspect ratio container

**Import Pattern**: `import { Button } from "@/components/ui/button"`

## Data Table System

### Core Components

#### 1. `DataTable<TData, TValue>` - Generic Data Table Component
**Location**: `resources/js/components/data-table/data-table.tsx`

Generic, reusable data table that accepts column definitions and data.

**Props**:
```typescript
interface DataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[];      // Column definitions
    data: TData[];                             // Array of data to display
    searchable?: boolean;                      // Enable global search (default: false)
    searchPlaceholder?: string;                // Search input placeholder
    onSearch?: (value: string) => void;        // Search callback
    enableColumnVisibility?: boolean;          // Show column visibility toggle (default: true)
    enableSorting?: boolean;                   // Enable sorting (default: true)
}
```

**Features**:
- ✅ Client-side sorting via TanStack Table
- ✅ Client-side filtering (global search)
- ✅ Column visibility toggle
- ✅ Uses shadcn/ui table primitives
- ✅ Fully typed with TypeScript generics
- ✅ Empty state handling

**Usage**:
```tsx
<DataTable
    columns={usersColumns}
    data={users.data}
    searchable={true}
    searchPlaceholder="Search users..."
    enableColumnVisibility={true}
    enableSorting={true}
/>
```

#### 2. `DataTableColumnHeader` - Sortable Column Header
**Location**: `resources/js/components/data-table/data-table.tsx`

Helper component for creating sortable column headers.

**Usage in column definitions**:
```tsx
{
    accessorKey: 'name',
    header: ({ column }) => (
        <DataTableColumnHeader column={column} title="Name" />
    ),
    // ...
}
```

### Column Definition Modules

Column definitions are resource-specific and located in:
```
resources/js/pages/{resource}/components/{resource}-columns.tsx
```

#### Example: Users Columns
**Location**: `resources/js/pages/users/components/users-columns.tsx`

```typescript
export interface User {
    id: number;
    uid: string;
    first_name: string;
    last_name: string;
    email: string;
    // ... other fields
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
                    {/* Custom cell rendering with shadcn/ui components */}
                </div>
            );
        },
    },
    // ... more columns
];
```

**Key Patterns**:
- Export interface for the resource type
- Export column definition array
- Use shadcn/ui components in cell renderers (Badge, Button, DropdownMenu, etc.)
- Use Wayfinder routes for navigation (`import { edit, destroy } from '@/routes/users'`)

## Pagination System

### Component: `LaravelPagination`
**Location**: `resources/js/components/pagination/laravel-pagination.tsx`

Laravel-compatible pagination component that works with standard Laravel paginator output.

#### Props Interface

```typescript
interface PaginatorMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    links: PaginationLink[];
    path: string;
    per_page: number;
    to: number | null;
    total: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface LaravelPaginationProps {
    meta: PaginatorMeta;
    className?: string;
    preserveScroll?: boolean;     // Preserve scroll position (default: true)
    preserveState?: boolean;      // Preserve component state (default: true)
    only?: string[];             // Inertia partial reload prop names
}
```

#### Features
- ✅ Works directly with Laravel paginator output
- ✅ Uses Inertia `<Link>` for navigation (no page reloads)
- ✅ Renders Previous/Next buttons
- ✅ Renders page numbers with ellipsis
- ✅ Uses shadcn/ui pagination primitives
- ✅ Handles disabled states
- ✅ Supports Inertia partial reloads

#### Usage

```tsx
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
    only={['users']}  // Only reload 'users' prop on page change
/>
```

#### Alternative: `SimpleLaravelPagination`

Direct props interface matching Laravel paginator structure:

```tsx
<SimpleLaravelPagination
    data={users.data}
    links={users.links}
    current_page={users.current_page}
    last_page={users.last_page}
    per_page={users.per_page}
    total={users.total}
    from={users.from}
    to={users.to}
    path={users.path}
    only={['users']}
/>
```

## Usage Guide

### Creating a New Resource Index Page

Follow these steps to create a data table for a new resource:

#### 1. Create Column Definitions

**File**: `resources/js/pages/{resource}/components/{resource}-columns.tsx`

```tsx
import { ColumnDef } from '@tanstack/react-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DataTableColumnHeader } from '@/components/data-table/data-table';
import { edit, destroy } from '@/routes/{resource}';

export interface Resource {
    id: number;
    name: string;
    // ... other fields
}

export const resourceColumns: ColumnDef<Resource>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Name" />
        ),
        cell: ({ row }) => {
            return <span>{row.getValue('name')}</span>;
        },
    },
    // ... more columns
    {
        id: 'actions',
        cell: ({ row }) => {
            // Action column with DropdownMenu
        },
    },
];
```

#### 2. Create Index Page

**File**: `resources/js/pages/{resource}/index.tsx`

```tsx
import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { DataTable } from '@/components/data-table/data-table';
import { LaravelPagination, PaginationLink } from '@/components/pagination/laravel-pagination';
import { Resource, resourceColumns } from './components/{resource}-columns';
import { create } from '@/routes/{resource}';

interface ResourceIndexProps {
    resources: {
        data: Resource[];
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

export default function ResourceIndex({ resources }: ResourceIndexProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Resources" />

            <div className="flex flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Resources
                        </h1>
                        <p className="text-muted-foreground">
                            Manage your resources.
                        </p>
                    </div>
                </div>

                <DataTable
                    columns={resourceColumns}
                    data={resources.data}
                    searchable={true}
                    enableColumnVisibility={true}
                    enableSorting={true}
                />

                <LaravelPagination
                    meta={{
                        current_page: resources.current_page,
                        from: resources.from,
                        last_page: resources.last_page,
                        links: resources.links,
                        path: resources.path,
                        per_page: resources.per_page,
                        to: resources.to,
                        total: resources.total,
                    }}
                    only={['resources']}
                />
            </div>
        </AppLayout>
    );
}
```

#### 3. Update Controller

**File**: `app/Http/Controllers/Web/ResourceController.php`

```php
public function index(): Response
{
    return Inertia::render('{resource}/index', [
        '{resources}' => ResourceService::paginate(15),
    ]);
}
```

#### 4. Update Service (if needed)

**File**: `app/Services/ResourceService.php`

```php
public function paginate(int $perPage = 15)
{
    return Resource::query()
        ->withCount(['relatedModel'])  // Optional: add counts
        ->latest()
        ->paginate($perPage);
}
```

#### 5. Generate Wayfinder Routes

```bash
php artisan wayfinder:generate
```

This generates route helpers in:
- `resources/js/routes/{resource}.ts`
- `resources/js/actions/App/Http/Controllers/Web/ResourceController.ts`

## Examples

### Complete Users Implementation

#### Backend Controller
**File**: `app/Http/Controllers/Web/UserController.php`

```php
public function index(): Response
{
    $this->authorize('viewAny', User::class);

    return Inertia::render('users/index', [
        'users' => $this->userService->paginate(15),
    ]);
}
```

#### Frontend Page
**File**: `resources/js/pages/users/index.tsx`

```tsx
import { Head } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/data-table/data-table';
import { LaravelPagination, PaginationLink } from '@/components/pagination/laravel-pagination';
import { User, usersColumns } from './components/users-columns';
import { create } from '@/routes/users';

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

export default function UsersIndex({ users }: UsersIndexProps) {
    return (
        <AppLayout breadcrumbs={[{ title: 'Users', href: '/users' }]}>
            <Head title="Users" />

            <div className="flex flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Users</h1>
                        <p className="text-muted-foreground">
                            Manage users and their access to your organization.
                        </p>
                    </div>
                    <Button onClick={() => router.visit(create.url())}>
                        <PlusIcon />
                        Add User
                    </Button>
                </div>

                <DataTable
                    columns={usersColumns}
                    data={users.data}
                    searchable={true}
                    searchPlaceholder="Search users..."
                    enableColumnVisibility={true}
                    enableSorting={true}
                />

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
```

#### Column Definitions
**File**: `resources/js/pages/users/components/users-columns.tsx`

```tsx
import { ColumnDef } from '@tanstack/react-table';
import { MoreHorizontalIcon, PencilIcon, TrashIcon } from 'lucide-react';
import { router } from '@inertiajs/react';
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
import { DataTableColumnHeader } from '@/components/data-table/data-table';
import { edit, destroy } from '@/routes/users';

export interface User {
    id: number;
    uid: string;
    name: string;
    email: string;
    email_verified_at: string | null;
    profile_photo_url: string;
    created_at: string;
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
                    <img
                        src={user.profile_photo_url}
                        alt={user.name}
                        className="size-8 rounded-full"
                    />
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
            const verified = row.original.email_verified_at !== null;
            return (
                <div className="flex items-center gap-2">
                    <span>{row.getValue('email')}</span>
                    {verified && (
                        <Badge variant="secondary">Verified</Badge>
                    )}
                </div>
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
                    {date.toLocaleDateString()}
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
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuItem
                                onClick={() => navigator.clipboard.writeText(user.email)}
                            >
                                Copy email
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                onClick={() => router.visit(edit.url(user.id))}
                            >
                                <PencilIcon />
                                Edit user
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                variant="destructive"
                                onClick={() => {
                                    if (confirm('Delete this user?')) {
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
```

### Complete Organizations Implementation

Similar structure with organizations-specific columns and interface. See:
- `resources/js/pages/organisations/index.tsx`
- `resources/js/pages/organisations/components/organisations-columns.tsx`
- `app/Http/Controllers/Web/OrganizationController.php`

## Best Practices

1. **Naming Convention**: Use kebab-case for all new files and directories
2. **Column Definitions**: Keep resource-specific, export interface + columns array
3. **shadcn/ui Only**: Use existing shadcn/ui components, don't create custom alternatives
4. **Type Safety**: Leverage TypeScript generics for type-safe tables
5. **SOLID Principles**:
   - DataTable: Single responsibility (render table)
   - Columns: Resource-specific logic separated
   - Pagination: Laravel integration only
6. **DRY**: Reuse DataTable, LaravelPagination, DataTableColumnHeader across all resources
7. **KISS**: Simple, clear component APIs without over-abstraction

## Dependencies

- `@tanstack/react-table` - ^8.x
- `@inertiajs/react` - ^2.x
- `lucide-react` - Icons
- All shadcn/ui components and their Radix UI dependencies

## File Structure Summary

```
resources/js/
├── components/
│   ├── data-table/
│   │   └── data-table.tsx                 # Generic data table + column header
│   ├── pagination/
│   │   └── laravel-pagination.tsx         # Laravel pagination component
│   └── ui/                                # shadcn/ui components (55+ components)
│       ├── button.tsx
│       ├── table.tsx
│       ├── badge.tsx
│       ├── dropdown-menu.tsx
│       └── ... (all other shadcn/ui components)
├── pages/
│   ├── users/
│   │   ├── index.tsx                      # Users index page
│   │   └── components/
│   │       └── users-columns.tsx          # Users column definitions
│   └── organisations/
│       ├── index.tsx                      # Organizations index page
│       └── components/
│           └── organisations-columns.tsx  # Organizations column definitions
└── routes/                                 # Generated by Wayfinder
    ├── users.ts
    └── organizations.ts

app/
└── Http/
    └── Controllers/
        └── Web/
            ├── UserController.php          # Returns paginated users
            └── OrganizationController.php  # Returns paginated organizations
```

## Conclusion

This system provides a complete, reusable solution for data tables and pagination in Juno, following all requirements:

✅ Strict adherence to shadcn/ui components
✅ Generic, reusable data table with TanStack Table v8
✅ Laravel-compatible pagination with Inertia integration
✅ Clean backend-frontend integration
✅ Kebab-case naming for all new files
✅ SOLID, DRY, and KISS principles
✅ Full TypeScript support
✅ Production-ready examples (users, organizations)
