<?php

namespace App\Services;

use App\Enums\Role as RoleEnum;
use App\Mail\OrganizationOwnerInvitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrganizationOnboardingService
{
    public function __construct(
        protected RolePermissionSeeder $rolePermissionSeeder,
        protected UserService $userService,
    ) {
    }

    /**
     * Onboard a new organization with roles, permissions, and owner.
     */
    public function onboard(Organization $organization, ?array $ownerData = null): void
    {
        if ($this->isAlreadyOnboarded($organization)) {
            return;
        }

        DB::transaction(function () use ($organization, $ownerData) {
            $this->rolePermissionSeeder->seed($organization);
            $owner = $this->setupOwner($organization, $ownerData);
            $this->sendOnboardingEmail($organization, $owner);
        });
    }

    /**
     * Check if organization has already been onboarded.
     */
    protected function isAlreadyOnboarded(Organization $organization): bool
    {
        return $organization->roles()->exists();
    }

    /**
     * Setup the owner user and assign them to the organization.
     */
    protected function setupOwner(Organization $organization, ?array $ownerData): User
    {
        // Get owner details from organization fields or passed data
        $email = $organization->owner_email ?? $ownerData['email'] ?? $organization->email;
        $firstName = $ownerData['first_name'] ?? $this->extractFirstName($organization->owner_name);
        $lastName = $ownerData['last_name'] ?? $this->extractLastName($organization->owner_name);
        $phone = $organization->owner_phone ?? $ownerData['phone'] ?? $organization->phone;

        $owner = $this->userService->findOrCreate($email, [
            'first_name' => $firstName ?? 'Owner',
            'last_name' => $lastName ?? '',
            'middle_name' => $ownerData['middle_name'] ?? null,
            'phone' => $phone,
        ]);

        // Attach owner to organization and mark as default
        $organization->addUser($owner);

        // Mark this as the user's default organization if they don't have one
        if (!$owner->current_organization_id) {
            $owner->setCurrentOrganization($organization);
        }

        // Set is_default flag on the pivot
        $organization->users()->updateExistingPivot($owner->id, ['is_default' => true]);

        $this->assignOwnerRole($organization, $owner);

        // Set organization contact details from owner if not set
        if (!$organization->email && $owner->email) {
            $organization->email = $owner->email;
        }

        if (!$organization->phone && $owner->phone) {
            $organization->phone = $owner->phone;
        }

        if ($organization->isDirty()) {
            $organization->save();
        }

        return $owner;
    }

    /**
     * Extract first name from full name.
     */
    protected function extractFirstName(?string $fullName): ?string
    {
        if (!$fullName) {
            return null;
        }

        return explode(' ', trim($fullName))[0] ?? null;
    }

    /**
     * Extract last name from full name.
     */
    protected function extractLastName(?string $fullName): ?string
    {
        if (!$fullName) {
            return null;
        }

        $parts = explode(' ', trim($fullName));

        return count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
    }

    /**
     * Assign the owner role to the user.
     */
    protected function assignOwnerRole(Organization $organization, User $owner): void
    {
        $ownerRole = $organization->roles()
            ->where('slug', RoleEnum::OWNER->value)
            ->first();

        if (!$ownerRole) {
            return;
        }

        $owner->roles()->syncWithoutDetaching([
            $ownerRole->id => ['organization_id' => $organization->id],
        ]);
    }

    /**
     * Send onboarding email to the owner.
     */
    protected function sendOnboardingEmail(Organization $organization, User $owner): void
    {
        try {
            Mail::to($owner->email)->queue(
                new OrganizationOwnerInvitation($organization, $owner)
            );
        } catch (\Throwable) {
            // Fail silently - email is not critical to onboarding
        }
    }
}
