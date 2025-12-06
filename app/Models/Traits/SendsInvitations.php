<?php

namespace App\Models\Traits;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

trait SendsInvitations
{
    /**
     * Send an invitation to join the organization.
     */
    public function inviteUser(
        string $email,
        string $role,
        ?User $inviter = null,
        ?\DateTimeInterface $expiresAt = null,
    ): Invitation {
        $invitation = $this->invitations()->create([
            'email' => $email,
            'token' => Str::random(64),
            'invited_by' => $inviter?->id,
            'role' => $role,
            'status' => 'pending',
            'expires_at' => $expiresAt ?? now()->addDays(7),
        ]);

        return $invitation;
    }

    /**
     * Accept an invitation and add the user to the organization.
     */
    public function acceptInvitation(Invitation $invitation, User $user): bool
    {
        if ($invitation->organization_id !== $this->id) {
            return false;
        }

        if ('pending' !== $invitation->status) {
            return false;
        }

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            $invitation->update(['status' => 'expired']);

            return false;
        }

        // Add user to organization
        $this->addUser($user);

        // Update invitation status
        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return true;
    }

    /**
     * Cancel a pending invitation.
     */
    public function cancelInvitation(Invitation $invitation): bool
    {
        if ($invitation->organization_id !== $this->id) {
            return false;
        }

        if ('pending' !== $invitation->status) {
            return false;
        }

        $invitation->update(['status' => 'cancelled']);

        return true;
    }

    /**
     * Resend an invitation with a new token and expiration.
     */
    public function resendInvitation(Invitation $invitation): bool
    {
        if ($invitation->organization_id !== $this->id) {
            return false;
        }

        if ('pending' !== $invitation->status) {
            return false;
        }

        $invitation->update([
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        return true;
    }

    /**
     * Get all pending invitations for the organization.
     */
    public function getPendingInvitations(): Collection
    {
        return $this->invitations()
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get();
    }

    /**
     * Get all expired invitations.
     */
    public function getExpiredInvitations(): Collection
    {
        return $this->invitations()
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();
    }

    /**
     * Clean up expired invitations.
     */
    public function cleanupExpiredInvitations(): int
    {
        return $this->invitations()
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    /**
     * Check if an email has a pending invitation.
     */
    public function hasPendingInvitation(string $email): bool
    {
        return $this->invitations()
            ->where('email', $email)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Find an invitation by token.
     */
    public function findInvitationByToken(string $token): ?Invitation
    {
        return $this->invitations()
            ->where('token', $token)
            ->where('status', 'pending')
            ->first();
    }
}
