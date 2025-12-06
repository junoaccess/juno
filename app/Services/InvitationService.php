<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationService
{
    public function paginate(int $perPage = 15)
    {
        return Invitation::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Invitation
    {
        return Invitation::create([
            'email' => $data['email'],
            'token' => Str::random(32),
            'invited_by' => $data['invited_by'],
            'role' => $data['role'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'expires_at' => $data['expires_at'] ?? now()->addDays(7),
            'organization_id' => $data['organization_id'],
        ]);
    }

    public function update(Invitation $invitation, array $data): Invitation
    {
        $invitation->update(array_filter([
            'email' => $data['email'] ?? null,
            'role' => $data['role'] ?? null,
            'status' => $data['status'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
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
}
