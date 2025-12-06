<?php

namespace App\Actions;

use App\Events\InvitationAccepted;
use App\Events\RolesAssignedToUser;
use App\Events\UserJoinedOrganisation;
use App\Exceptions\InvitationException;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AcceptInvitationAction
{
    /**
     * Accept an invitation and attach user to organisation.
     *
     * @throws InvitationException
     */
    public function execute(Invitation $invitation, User $user): void
    {
        if (! $invitation->canBeAccepted()) {
            throw InvitationException::cannotBeAccepted($invitation);
        }

        DB::transaction(function () use ($invitation, $user) {
            $isNewMember = $this->attachUserToOrganisation($invitation, $user);
            $this->setDefaultOrganisationIfNeeded($invitation, $user);
            $roleIds = $this->assignRolesToUser($invitation, $user);

            // Dispatch domain events
            if ($isNewMember) {
                UserJoinedOrganisation::dispatch($user, $invitation->organization);
            }

            if (! empty($roleIds)) {
                RolesAssignedToUser::dispatch($user, $invitation->organization, $roleIds);
            }

            InvitationAccepted::dispatch($invitation, $user);
        });
    }

    /**
     * Attach user to organisation if not already a member.
     */
    protected function attachUserToOrganisation(Invitation $invitation, User $user): bool
    {
        if ($invitation->organization->users()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $invitation->organization->users()->attach($user->id, [
            'is_default' => $user->organizations()->count() === 0,
        ]);

        return true;
    }

    /**
     * Set as current organisation if user doesn't have one.
     */
    protected function setDefaultOrganisationIfNeeded(Invitation $invitation, User $user): void
    {
        if (! $user->current_organization_id) {
            $user->setCurrentOrganization($invitation->organization);
        }
    }

    /**
     * Assign roles from invitation to user.
     *
     * @return array<int> The role IDs that were assigned
     */
    protected function assignRolesToUser(Invitation $invitation, User $user): array
    {
        $organization = $invitation->organization;
        $roleIds = [];

        foreach ($invitation->roles ?? [] as $roleName) {
            $role = $organization->roles()
                ->where('name', $roleName)
                ->orWhere('slug', $roleName)
                ->first();

            if ($role) {
                $roleIds[$role->id] = ['organization_id' => $organization->id];
            }
        }

        if (! empty($roleIds)) {
            $user->roles()->syncWithoutDetaching($roleIds);
        }

        return array_keys($roleIds);
    }
}
