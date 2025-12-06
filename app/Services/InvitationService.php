<?php

namespace App\Services;

use App\Mail\OrganizationInvitationMail;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationService
{
    /**
     * Paginate invitations.
     */
    public function paginate(int $perPage = 15)
    {
        return Invitation::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create and send an invitation.
     */
    public function create(array $data): Invitation
    {
        // Support both new format and legacy format
        if (isset($data['organization']) && $data['organization'] instanceof Organization) {
            return $this->createWithToken(
                $data['organization'],
                $data['email'],
                $data['roles'] ?? [],
                $data['invited_by'],
                $data['name'] ?? null
            );
        }

        // Legacy format support
        return Invitation::create([
            'email' => $data['email'],
            'token_hash' => hash('sha256', Str::random(64)),
            'invited_by' => $data['invited_by'],
            'roles' => $data['roles'] ?? $data['role'] ?? [],
            'name' => $data['name'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'expires_at' => $data['expires_at'] ?? now()->addDays(7),
            'organization_id' => $data['organization_id'],
        ]);
    }

    /**
     * Create invitation with secure token and send email.
     */
    public function createWithToken(Organization $organization, string $email, array $roles, User $invitedBy, ?string $name = null): Invitation
    {
        // Invalidate any existing pending invitations for this org+email
        $this->invalidateExistingInvitations($organization, $email);

        // Generate secure token
        $rawToken = $this->generateSecureToken();
        $tokenHash = $this->hashToken($rawToken);

        // Create invitation
        $invitation = $organization->invitations()->create([
            'email' => $email,
            'name' => $name,
            'token_hash' => $tokenHash,
            'roles' => $roles,
            'status' => 'pending',
            'expires_at' => now()->addDays(config('auth.invitation_expiry_days', 7)),
            'invited_by' => $invitedBy->id,
        ]);

        // Send invitation email
        $this->sendInvitationEmail($invitation, $rawToken);

        return $invitation;
    }

    /**
     * Accept an invitation and attach user to organization.
     */
    public function accept(Invitation $invitation, User $user): void
    {
        if (!$invitation->canBeAccepted()) {
            throw new \RuntimeException('This invitation cannot be accepted.');
        }

        DB::transaction(function () use ($invitation, $user) {
            // Attach user to organization if not already a member
            if (!$invitation->organization->users()->where('user_id', $user->id)->exists()) {
                $invitation->organization->users()->attach($user->id, [
                    'is_default' => 0 === $user->organizations()->count(),
                ]);
            }

            // Set as current organization if user doesn't have one
            if (!$user->current_organization_id) {
                $user->setCurrentOrganization($invitation->organization);
            }

            // Attach roles to user for this organization
            $this->attachRoles($invitation, $user);

            // Mark invitation as accepted
            $invitation->markAsAccepted();
        });
    }

    /**
     * Find invitation by raw token.
     */
    public function findByToken(string $rawToken): ?Invitation
    {
        $tokenHash = $this->hashToken($rawToken);

        return Invitation::where('token_hash', $tokenHash)->first();
    }

    public function update(Invitation $invitation, array $data): Invitation
    {
        $invitation->update(array_filter([
            'email' => $data['email'] ?? null,
            'roles' => $data['roles'] ?? $data['role'] ?? null,
            'status' => $data['status'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'name' => $data['name'] ?? null,
        ]));

        return $invitation->fresh();
    }

    public function delete(Invitation $invitation): bool
    {
        return $invitation->delete();
    }

    public function restore(Invitation $invitation): bool
    {
        return $invitation->restore();
    }

    public function loadRelationships(Invitation $invitation): Invitation
    {
        return $invitation->load(['organization', 'inviter']);
    }

    /**
     * Generate a secure random token.
     */
    protected function generateSecureToken(): string
    {
        return Str::random(64);
    }

    /**
     * Hash a token for storage.
     */
    protected function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Invalidate existing pending invitations for organization+email.
     */
    protected function invalidateExistingInvitations(Organization $organization, string $email): void
    {
        $organization->invitations()
            ->where('email', $email)
            ->where('status', 'pending')
            ->update(['status' => 'revoked']);
    }

    /**
     * Attach roles from invitation to user.
     */
    protected function attachRoles(Invitation $invitation, User $user): void
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

        if (!empty($roleIds)) {
            $user->roles()->syncWithoutDetaching($roleIds);
        }
    }

    /**
     * Send invitation email.
     */
    protected function sendInvitationEmail(Invitation $invitation, string $rawToken): void
    {
        try {
            Mail::to($invitation->email)->queue(
                new OrganizationInvitationMail($invitation, $rawToken)
            );
        } catch (\Throwable) {
            // Fail silently - email is not critical
        }
    }
}
