<?php

namespace App\Filters;

use Filterable\Filter;
use Illuminate\Http\Request;

abstract class BaseFilter extends Filter
{
    /**
     * Default sortable columns shared across filters.
     */
    protected array $defaultSortableColumns = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Additional sortable columns specific to the filter.
     */
    protected array $sortableColumns = [];

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->enableFeatures([
            'validation',
            'optimization',
            'filterChaining',
        ]);
    }

    /**
     * Sort the results by a given column and direction.
     */
    protected function sort(string $value): void
    {
        [$column, $direction] = array_pad(explode(':', $value), 2, 'asc');
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        $allowedColumns = array_merge($this->defaultSortableColumns, $this->sortableColumns);

        if (! in_array($column, $allowedColumns)) {
            $column = 'created_at';
        }

        $this->getBuilder()->orderBy($column, $direction);
    }

    /**
     * Get all sortable columns.
     */
    protected function getSortableColumns(): array
    {
        return array_merge($this->defaultSortableColumns, $this->sortableColumns);
    }
}
