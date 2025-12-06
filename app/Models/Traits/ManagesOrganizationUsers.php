<?php

namespace App\Models\Traits;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

trait ManagesOrganizationUsers
{
    /**
     * Add a user to an organization (explicit target organization).
     */
    public function addUserToOrganization(User $user, Organization $organization, array $attributes = []): Pivot
    {
        $organization->users()->attach($user->getKey(), $attributes);

        return $organization->users()->where('user_id', $user->getKey())->first()->pivot;
    }

    /**
     * Remove a user from an organization (explicit target organization).
     */
    public function removeUserFromOrganization(User $user, Organization $organization): void
    {
        $organization->users()->detach($user->getKey());
    }

    /**
     * Determine whether a user belongs to an organization (explicit target organization).
     */
    public function isUserInOrganization(User $user, Organization $organization): bool
    {
        return $organization->users()->where('user_id', $user->getKey())->exists();
    }

    /**
     * Add a user to the current model (when the trait is used on Organization instance).
     */
    public function addUser(User $user): bool
    {
        if ($this->hasUser($user)) {
            return false;
        }

        $this->users()->attach($user);

        return true;
    }

    /**
     * Remove a user from the current model (when the trait is used on Organization instance).
     */
    public function removeUser(User $user): bool
    {
        if (!$this->hasUser($user)) {
            return false;
        }

        $this->users()->detach($user);

        return true;
    }

    /**
     * Check if the organization has a specific user.
     */
    public function hasUser(User $user): bool
    {
        return $this->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Get all users in the organization.
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Get the count of users in the organization.
     */
    public function getUsersCount(): int
    {
        return $this->users()->count();
    }

    /**
     * Sync users with the organization.
     */
    public function syncUsers(array $userIds): array
    {
        return $this->users()->sync($userIds);
    }
}
