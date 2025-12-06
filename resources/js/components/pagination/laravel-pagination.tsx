import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatorMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    links: PaginationLink[];
    path: string;
    per_page: number;
    to: number | null;
    total: number;
}

export interface LaravelPaginationProps {
    meta: PaginatorMeta;
    className?: string;
    preserveScroll?: boolean;
    preserveState?: boolean;
    only?: string[];
}

export function LaravelPagination({
    meta,
    className,
    preserveScroll = true,
    preserveState = true,
    only = [],
}: LaravelPaginationProps) {
    const { links, last_page } = meta;

    if (last_page <= 1) {
        return null;
    }

    const renderPageLink = (link: PaginationLink, index: number) => {
        const isEllipsis = link.label === '...';
        const isPrevious = link.label.toLowerCase().includes('previous');
        const isNext = link.label.toLowerCase().includes('next');

        // Previous button
        if (isPrevious) {
            if (!link.url) {
                return (
                    <PaginationItem key={`prev-${index}`}>
                        <PaginationPrevious className="pointer-events-none opacity-50" />
                    </PaginationItem>
                );
            }
            return (
                <PaginationItem key={`prev-${index}`}>
                    <Link
                        href={link.url}
                        preserveScroll={preserveScroll}
                        preserveState={preserveState}
                        only={only}
                    >
                        <PaginationPrevious />
                    </Link>
                </PaginationItem>
            );
        }

        // Next button
        if (isNext) {
            if (!link.url) {
                return (
                    <PaginationItem key={`next-${index}`}>
                        <PaginationNext className="pointer-events-none opacity-50" />
                    </PaginationItem>
                );
            }
            return (
                <PaginationItem key={`next-${index}`}>
                    <Link
                        href={link.url}
                        preserveScroll={preserveScroll}
                        preserveState={preserveState}
                        only={only}
                    >
                        <PaginationNext />
                    </Link>
                </PaginationItem>
            );
        }

        // Ellipsis
        if (isEllipsis) {
            return (
                <PaginationItem key={`ellipsis-${index}`}>
                    <span className="flex size-9 items-center justify-center">
                        ...
                    </span>
                </PaginationItem>
            );
        }

        // Page number
        const pageNumber = parseInt(link.label, 10);
        if (!link.url) {
            return (
                <PaginationItem key={`page-${pageNumber}-${index}`}>
                    <PaginationLink
                        isActive={link.active}
                        className="pointer-events-none"
                    >
                        {pageNumber}
                    </PaginationLink>
                </PaginationItem>
            );
        }

        return (
            <PaginationItem key={`page-${pageNumber}-${index}`}>
                <Link
                    href={link.url}
                    preserveScroll={preserveScroll}
                    preserveState={preserveState}
                    only={only}
                >
                    <PaginationLink isActive={link.active}>
                        {pageNumber}
                    </PaginationLink>
                </Link>
            </PaginationItem>
        );
    };

    return (
        <Pagination className={cn('justify-end', className)}>
            <PaginationContent>
                {links.map((link, index) => renderPageLink(link, index))}
            </PaginationContent>
        </Pagination>
    );
}

// Alternative simplified interface for direct use with Laravel paginator props
export interface SimplePaginationProps<T = unknown> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    path: string;
    className?: string;
    preserveScroll?: boolean;
    preserveState?: boolean;
    only?: string[];
}

export function SimpleLaravelPagination<T = unknown>({
    links,
    current_page,
    last_page,
    per_page,
    total,
    from,
    to,
    path,
    className,
    preserveScroll,
    preserveState,
    only,
}: SimplePaginationProps<T>) {
    const meta: PaginatorMeta = {
        current_page,
        last_page,
        per_page,
        total,
        from,
        to,
        links,
        path,
    };

    return (
        <LaravelPagination
            meta={meta}
            className={className}
            preserveScroll={preserveScroll}
            preserveState={preserveState}
            only={only}
        />
    );
}
