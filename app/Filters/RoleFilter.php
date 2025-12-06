<?php

namespace App\Filters;

use Illuminate\Http\Request;

class RoleFilter extends BaseFilter
{
    protected array $filters = [
        'search',
        'sort',
    ];

    protected array $sortableColumns = [
        'name',
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->select([
            'id',
            'name',
            'guard_name',
            'created_at',
        ]);
    }

    /**
     * Filter by search term across name.
     */
    protected function search(string $value): void
    {
        $this->getBuilder()->where('name', 'like', "%{$value}%");
    }
}
