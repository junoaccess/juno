<?php

namespace App\Services;

use App\Filters\UserFilter;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Get paginated users with optional filtering.
     */
    public function paginate(int $perPage = 15, ?UserFilter $filter = null): LengthAwarePaginator
    {
        $query = User::query()->with(['currentOrganization:id,name,slug']);

        if ($filter) {
            $query = $query->filter($filter);
        } else {
            $query = $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all users for a specific organization.
     */
    public function forOrganization(int $organizationId, int $perPage = 15)
    {
        return User::withoutGlobalScopes()
            ->forOrganization($organizationId)
            ->latest()
            ->paginate($perPage);
    }

    public function findOrCreate(string $email, array $attributes = []): User
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return $user;
        }

        return $this->create(array_merge(['email' => $email], $attributes));
    }

    public function create(array $attributes): User
    {
        return User::create([
            'uid' => Str::uuid(),
            'first_name' => $attributes['first_name'] ?? 'User',
            'last_name' => $attributes['last_name'] ?? '',
            'middle_name' => $attributes['middle_name'] ?? null,
            'email' => $attributes['email'],
            'phone' => $attributes['phone'] ?? null,
            'password' => $attributes['password'] ?? Hash::make(Str::random(32)),
            'email_verified_at' => $attributes['email_verified_at'] ?? now(),
        ]);
    }

    public function update(User $user, array $data): User
    {
        $user->update(array_filter([
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'middle_name' => $data['middle_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
        ]));

        if (isset($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function restore(User $user): bool
    {
        return $user->restore();
    }

    public function loadRelationships(User $user): User
    {
        return $user->load([
            'organizations:id,name,slug',
            'teams:id,name,organization_id',
            'roles:id,name,slug,organization_id',
        ]);
    }
}
