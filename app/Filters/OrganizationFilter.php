<?php

namespace App\Filters;

use Illuminate\Http\Request;

class OrganizationFilter extends BaseFilter
{
    protected array $filters = [
        'search',
        'sort',
        'email',
        'website',
    ];

    protected array $sortableColumns = [
        'name',
        'email',
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->select([
            'id',
            'name',
            'slug',
            'email',
            'phone',
            'website',
            'created_at',
        ])->with(['users', 'teams']);
    }

    /**
     * Filter by search term across name, email, and owner name.
     */
    protected function search(string $value): void
    {
        $this->getBuilder()->where(function ($query) use ($value) {
            $query->where('name', 'like', "%{$value}%")
                ->orWhere('email', 'like', "%{$value}%")
                ->orWhere('owner_name', 'like', "%{$value}%");
        });
    }

    /**
     * Filter by email address.
     */
    protected function email(string $value): void
    {
        $this->getBuilder()->where('email', 'like', "%{$value}%");
    }

    /**
     * Filter by website.
     */
    protected function website(string $value): void
    {
        $this->getBuilder()->where('website', 'like', "%{$value}%");
    }
}
