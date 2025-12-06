<?php

namespace App\Filters;

use Illuminate\Http\Request;

class InvitationFilter extends BaseFilter
{
    protected array $filters = [
        'search',
        'sort',
        'status',
    ];

    protected array $sortableColumns = [
        'name',
        'email',
        'status',
        'expires_at',
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->select([
            'id',
            'email',
            'name',
            'status',
            'roles',
            'invited_by',
            'expires_at',
            'accepted_at',
            'created_at',
        ]);
    }

    /**
     * Filter by search term across name and email.
     */
    protected function search(string $value): void
    {
        $this->getBuilder()->where(function ($query) use ($value) {
            $query->where('name', 'like', "%{$value}%")
                ->orWhere('email', 'like', "%{$value}%");
        });
    }

    /**
     * Filter by invitation status.
     */
    protected function status(string $value): void
    {
        $this->getBuilder()->where('status', $value);
    }
}
