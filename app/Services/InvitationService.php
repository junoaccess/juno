<?php

namespace App\Services;

use App\Actions\AcceptInvitationAction;
use App\Events\InvitationCreated;
use App\Filters\InvitationFilter;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class InvitationService
{
    public function __construct(
        protected AcceptInvitationAction $acceptInvitationAction,
    ) {}

    /**
     * Paginate invitations with optional filtering.
     */
    public function paginate(int $perPage = 15, ?InvitationFilter $filter = null): LengthAwarePaginator
    {
        $query = Invitation::query()->with([
            'organization:id,name,slug',
            'inviter:id,first_name,last_name,email',
        ]);

        if ($filter) {
            $query = $query->filter($filter);
        } else {
            $query = $query->latest();
        }

        return $query->paginate($perPage);
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

        // Dispatch event to send invitation email (decoupled)
        InvitationCreated::dispatch($invitation, $rawToken);

        return $invitation;
    }

    /**
     * Accept an invitation and attach user to organization.
     */
    public function accept(Invitation $invitation, User $user): void
    {
        $this->acceptInvitationAction->execute($invitation, $user);
    }

    /**
     * Find invitation by raw token.
     */
    public function findByToken(string $rawToken): ?Invitation
    {
        $tokenHash = $this->hashToken($rawToken);

        return Invitation::withoutGlobalScopes()->where('token_hash', $tokenHash)->first();
    }

    /**
     * Validate invitation is valid, pending, and not expired.
     */
    public function validateInvitation(Invitation $invitation): void
    {
        if ($invitation->status !== 'pending') {
            abort(403, 'This invitation has already been used or revoked.');
        }

        if ($invitation->expires_at->isPast()) {
            abort(403, 'This invitation has expired.');
        }
    }

    /**
     * Get invitation URL for the organization subdomain.
     */
    public function getInvitationUrl(Invitation $invitation): string
    {
        return "https://{$invitation->organization->slug}.".config('app.main_domain').'/dashboard';
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
        return $invitation->load([
            'organization:id,name,slug',
            'inviter:id,first_name,last_name,email',
        ]);
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
}
