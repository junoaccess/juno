import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Column,
    ColumnDef,
    ColumnFiltersState,
    SortingState,
    VisibilityState,
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import {
    ArrowDownIcon,
    ArrowUpDownIcon,
    ArrowUpIcon,
    EyeOffIcon,
} from 'lucide-react';
import * as React from 'react';

interface DataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[];
    data: TData[];
    searchable?: boolean;
    searchPlaceholder?: string;
    onSearch?: (value: string) => void;
    enableColumnVisibility?: boolean;
    enableSorting?: boolean;
}

export function DataTable<TData, TValue>({
    columns,
    data,
    searchable = false,
    searchPlaceholder = 'Search...',
    onSearch,
    enableColumnVisibility = true,
    enableSorting = true,
}: DataTableProps<TData, TValue>) {
    const [sorting, setSorting] = React.useState<SortingState>([]);
    const [columnFilters, setColumnFilters] =
        React.useState<ColumnFiltersState>([]);
    const [columnVisibility, setColumnVisibility] =
        React.useState<VisibilityState>({});
    const [globalFilter, setGlobalFilter] = React.useState('');

    // eslint-disable-next-line react-hooks/incompatible-library
    const table = useReactTable({
        data,
        columns,
        getCoreRowModel: getCoreRowModel(),
        onSortingChange: setSorting,
        getSortedRowModel: enableSorting ? getSortedRowModel() : undefined,
        onColumnFiltersChange: setColumnFilters,
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setColumnVisibility,
        onGlobalFilterChange: setGlobalFilter,
        state: {
            sorting,
            columnFilters,
            columnVisibility,
            globalFilter,
        },
    });

    const handleSearchChange = React.useCallback(
        (value: string) => {
            setGlobalFilter(value);
            onSearch?.(value);
        },
        [onSearch],
    );

    return (
        <div className="w-full space-y-4">
            <div className="flex items-center justify-between gap-2">
                {searchable && (
                    <Input
                        placeholder={searchPlaceholder}
                        value={globalFilter ?? ''}
                        onChange={(event) =>
                            handleSearchChange(event.target.value)
                        }
                        className="max-w-sm"
                    />
                )}
                {enableColumnVisibility && (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" size="sm">
                                <EyeOffIcon />
                                Columns
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" className="w-[150px]">
                            {table
                                .getAllColumns()
                                .filter((column) => column.getCanHide())
                                .map((column) => {
                                    return (
                                        <DropdownMenuCheckboxItem
                                            key={column.id}
                                            className="capitalize"
                                            checked={column.getIsVisible()}
                                            onCheckedChange={(value) =>
                                                column.toggleVisibility(!!value)
                                            }
                                        >
                                            {column.id}
                                        </DropdownMenuCheckboxItem>
                                    );
                                })}
                        </DropdownMenuContent>
                    </DropdownMenu>
                )}
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => {
                                    return (
                                        <TableHead key={header.id}>
                                            {header.isPlaceholder
                                                ? null
                                                : flexRender(
                                                      header.column.columnDef
                                                          .header,
                                                      header.getContext(),
                                                  )}
                                        </TableHead>
                                    );
                                })}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {table.getRowModel().rows?.length ? (
                            table.getRowModel().rows.map((row) => (
                                <TableRow
                                    key={row.id}
                                    data-state={
                                        row.getIsSelected() && 'selected'
                                    }
                                >
                                    {row.getVisibleCells().map((cell) => (
                                        <TableCell key={cell.id}>
                                            {flexRender(
                                                cell.column.columnDef.cell,
                                                cell.getContext(),
                                            )}
                                        </TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell
                                    colSpan={columns.length}
                                    className="h-24 text-center"
                                >
                                    No results.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </div>
        </div>
    );
}

export function DataTableColumnHeader<TData, TValue>({
    column,
    title,
    className,
}: {
    column: Column<TData, TValue>;
    title: string;
    className?: string;
}) {
    if (!column.getCanSort()) {
        return <div className={className}>{title}</div>;
    }

    return (
        <div className={className}>
            <Button
                variant="ghost"
                size="sm"
                className="-ml-3 h-8 data-[state=open]:bg-accent"
                onClick={() =>
                    column.toggleSorting(column.getIsSorted() === 'asc')
                }
            >
                <span>{title}</span>
                {column.getIsSorted() === 'desc' ? (
                    <ArrowDownIcon className="ml-2" />
                ) : column.getIsSorted() === 'asc' ? (
                    <ArrowUpIcon className="ml-2" />
                ) : (
                    <ArrowUpDownIcon className="ml-2" />
                )}
            </Button>
        </div>
    );
}
