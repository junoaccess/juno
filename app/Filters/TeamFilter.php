<?php

namespace App\Filters;

use Illuminate\Http\Request;

class TeamFilter extends BaseFilter
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
            'slug',
            'description',
            'organization_id',
            'created_at',
        ]);
    }

    /**
     * Filter by search term across name and description.
     */
    protected function search(string $value): void
    {
        $this->getBuilder()->where(function ($query) use ($value) {
            $query->where('name', 'like', "%{$value}%")
                ->orWhere('description', 'like', "%{$value}%");
        });
    }
}
