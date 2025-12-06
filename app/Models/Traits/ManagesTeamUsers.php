<?php

namespace App\Models\Traits;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait ManagesTeamUsers
{
    /**
     * Add a user to a specific team (explicit target team).
     */
    public function addUserToTeam(User $user, Team $team, array $attributes = []): void
    {
        $team->users()->attach($user->getKey(), $attributes);
    }

    /**
     * Remove a user from a specific team (explicit target team).
     */
    public function removeUserFromTeam(User $user, Team $team): void
    {
        $team->users()->detach($user->getKey());
    }

    /**
     * Determine whether a user belongs to a specific team (explicit target team).
     */
    public function isUserInTeam(User $user, Team $team): bool
    {
        return $team->users()->where('user_id', $user->getKey())->exists();
    }

    /**
     * Add a user to the current team instance.
     */
    public function addUser(User $user): bool
    {
        // If the team is associated with an organization, ensure the user belongs to it.
        if (isset($this->organization) && !$this->organization->hasUser($user)) {
            return false;
        }

        if ($this->users()->where('users.id', $user->id)->exists()) {
            return false;
        }

        $this->users()->attach($user);

        return true;
    }

    /**
     * Remove a user from the current team instance.
     */
    public function removeUser(User $user): bool
    {
        if (!$this->users()->where('users.id', $user->id)->exists()) {
            return false;
        }

        $this->users()->detach($user);

        return true;
    }

    /**
     * Check if the team has a specific user.
     */
    public function hasUser(User $user): bool
    {
        return $this->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Get all users in the team.
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Get the count of users in the team.
     */
    public function getUsersCount(): int
    {
        return $this->users()->count();
    }

    /**
     * Sync users with the team (only users from the same organization will be synced.
     * Returns the result of the sync call.
     */
    public function syncUsers(array $userIds): array
    {
        if (!isset($this->organization)) {
            return $this->users()->sync($userIds);
        }

        $validUserIds = $this->organization
            ->users()
            ->whereIn('users.id', $userIds)
            ->pluck('users.id')
            ->toArray();

        return $this->users()->sync($validUserIds);
    }
}
