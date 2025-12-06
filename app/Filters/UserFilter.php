<?php

namespace App\Filters;

use Illuminate\Http\Request;

class UserFilter extends BaseFilter
{
    protected array $filters = [
        'search',
        'sort',
        'email',
        'status',
    ];

    protected array $sortableColumns = [
        'first_name',
        'last_name',
        'email',
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->select([
            'id',
            'uid',
            'first_name',
            'last_name',
            'email',
            'email_verified_at',
            'created_at',
        ]);
    }

    /**
     * Filter by search term across name and email.
     */
    protected function search(string $value): void
    {
        $this->getBuilder()->where(function ($query) use ($value) {
            $query->where('first_name', 'like', "%{$value}%")
                ->orWhere('last_name', 'like', "%{$value}%")
                ->orWhere('email', 'like', "%{$value}%");
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
     * Filter by verification status.
     */
    protected function status(string $value): void
    {
        match ($value) {
            'verified' => $this->getBuilder()->whereNotNull('email_verified_at'),
            'unverified' => $this->getBuilder()->whereNull('email_verified_at'),
            default => null,
        };
    }
}
